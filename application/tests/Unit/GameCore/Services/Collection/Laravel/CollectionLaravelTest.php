<?php

namespace Tests\Unit\GameCore\Services\Collection\Laravel;

use App\GameCore\Services\Collection\Collection;
use App\GameCore\Services\Collection\CollectionException;
use App\GameCore\Services\Collection\Laravel\CollectionLaravel;
use PHPUnit\Framework\TestCase;

class CollectionLaravelTest extends TestCase
{
    protected Collection $collection;
    protected Collection $emptyCollection;
    protected Collection $keyCollection;

    public function setUp(): void
    {
        $this->collection = new CollectionLaravel([1, 2, 3]);
        $this->emptyCollection = new CollectionLaravel();
        $this->keyCollection = new CollectionLaravel(['a' => 1, 'b' => 2, 'c' => 3]);
    }

    public function testInstanceOfCollection(): void
    {
        $this->assertInstanceOf(Collection::class, $this->collection);
        $this->assertInstanceOf(Collection::class, $this->emptyCollection);
    }

    public function testCount(): void
    {
        $this->assertEquals(3, $this->collection->count());
        $this->assertEquals(0, $this->emptyCollection->count());
    }

    public function testIsEmpty(): void
    {
        $this->assertFalse($this->collection->isEmpty());
        $this->assertTrue($this->emptyCollection->isEmpty());
    }

    public function testExist(): void
    {
        $this->assertFalse($this->emptyCollection->exist('missing-key'));
        $this->assertTrue($this->collection->exist(1));
        $this->assertTrue($this->keyCollection->exist('a'));
        $this->assertFalse($this->keyCollection->exist(1));
    }

    public function testToArray(): void
    {
        $this->assertEquals([1, 2, 3], $this->collection->toArray());
        $this->assertEquals([], $this->emptyCollection->toArray());
        $this->assertEquals(['a' => 1, 'b' => 2, 'c' => 3], $this->keyCollection->toArray());
    }

    public function testEach(): void
    {
        $this->assertEquals([2, 3, 4], $this->collection->each(fn($element) => $element + 1)->toArray());
        $this->assertEquals([], $this->emptyCollection->each(fn($element) => 'no matter what')->toArray());
        $this->assertEquals(
            ['a' => null, 'b' => null, 'c' => null],
            $this->keyCollection->each(fn($element) => null)->toArray()
        );
    }

    public function testFilter(): void
    {
        $this->assertEquals([1 => 2, 2 => 3], $this->collection->filter(fn($element) => $element > 1)->toArray());
        $this->assertEquals([0 => 1, 2 => 3], $this->collection->filter(fn($element) => $element !== 2)->toArray());
        $this->assertEquals(['a' => 1, 'b' => 2], $this->keyCollection->filter(fn($element) => $element < 3)->toArray());
    }

    public function testShuffle(): void
    {
        $original = clone $this->collection;
        $orderChanged = false;

        for ($i = 0; $i < 100; $i++) {
            $shuffled = $this->collection->shuffle();
            if ($original->toArray() !== $shuffled->toArray()) {
                $orderChanged = true;
                break;
            }
        }

        $this->assertTrue($orderChanged);
        $this->assertEquals(3, $this->collection->count());
    }

    public function testRandom(): void
    {
        $initial = $this->collection->random();
        $updated = false;

        for ($i = 0; $i < 100; $i++) {
            if ($initial !== $this->collection->random()) {
                $updated = true;
                break;
            }
        }

        $this->assertTrue($updated);
    }

    public function testAssignKeys(): void
    {
        $this->collection->assignKeys(fn($item, $key) => $item * 2);
        $this->assertEquals([2 => 1, 4 => 2, 6 => 3], $this->collection->toArray());
    }

    public function testReset(): void
    {
        $newElements = [4, 5];
        $this->assertEquals($newElements, $this->collection->reset($newElements)->toArray());
        $this->assertEquals([], $this->collection->reset([])->toArray());
    }

    public function testThrowExceptionWhenOverwritingSingleElementWithKey(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_DUPLICATE);

        $this->collection->add(1, 0);
    }

    public function testAddWithKey(): void
    {
        $this->assertTrue($this->collection->add(4, 3)->exist(3));
        $this->assertEquals([1, 2, 3, 4, 5], $this->collection->add(5)->toArray());
    }

    public function testGetOneThrowExceptionWithMissingKey(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_MISSING_KEY);

        $this->collection->getOne(4);
    }

    public function testGetOne(): void
    {
        $this->assertEquals(1, $this->keyCollection->getOne('a'));
    }

    public function testRemoveOneThrowExceptionWithMissingKey(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_MISSING_KEY);

        $this->collection->RemoveOne(4);
    }

    public function testRemoveOne(): void
    {
        $this->collection->removeOne(0);
        $this->assertEquals([1 => 2, 2 => 3], $this->collection->toArray());
        $this->assertFalse($this->collection->exist(0));
    }

    public function testRemoveAll(): void
    {
        $this->collection->removeAll();
        $this->assertEquals(0, $this->collection->count());
    }

    public function testPullFirstThrowExceptionIfCollectionEmpty(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_NO_ELEMENTS);

        $this->emptyCollection->pullFirst();
    }

    public function testPullFirst(): void
    {
        $this->assertEquals(1, $this->keyCollection->pullFirst());
        $this->assertEquals(['b' => 2, 'c' => 3], $this->keyCollection->toArray());
    }

    public function testPullLastThrowExceptionIfCollectionEmpty(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_NO_ELEMENTS);

        $this->emptyCollection->pullLast();
    }

    public function testPullLast(): void
    {
        $this->assertEquals(3, $this->collection->pullLast());
        $this->assertEquals([1, 2], $this->collection->toArray());
    }
}
