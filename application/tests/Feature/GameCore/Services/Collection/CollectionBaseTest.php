<?php

namespace Tests\Feature\GameCore\Services\Collection;

use App\GameCore\Services\Collection\Collection;
use App\GameCore\Services\Collection\CollectionBase;
use App\GameCore\Services\Collection\CollectionException;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class CollectionBaseTest extends TestCase
{
    protected Collection $handler;
    protected array $elements = [1, 2];
    protected Collection $collection;

    public function setUp(): void
    {
        parent::setUp();

        $this->handler = App::make(Collection::class);
        $this->collection = new CollectionBase($this->handler, $this->elements);
    }

    protected function getExpectedToArray(): array
    {
        return [
            0 => $this->elements[0],
            1 => $this->elements[1],
        ];
    }

    public function testInstanceOfClasses(): void
    {
        $this->assertInstanceOf(Collection::class, $this->collection);
        $this->assertInstanceOf(CollectionBase::class, $this->collection);
    }

    public function testCount(): void
    {
        $this->assertEquals(2, $this->collection->count());
    }

    public function testIsEmpty(): void
    {
        $this->assertFalse($this->collection->isEmpty());
        $this->assertTrue((new CollectionBase($this->handler))->isEmpty());
    }

    public function testExist(): void
    {
        $this->assertFalse($this->collection->exist('missing-key'));
        $this->assertTrue($this->collection->exist(0));
        $this->assertTrue($this->collection->exist(1));
    }

    public function testToArray(): void
    {
        $this->assertEquals($this->getExpectedToArray(), $this->collection->toArray());
    }

    public function testEach(): void
    {
        $this->collection->each(fn($element) => $element * 2);
        $this->assertEquals([2, 4], $this->collection->toArray());
    }

    public function testFilter(): void
    {
        $filtered = $this->collection->filter(fn($element) => $element > 1);
        $this->assertEquals(1, $filtered->count());
        $this->assertEquals(2, $this->collection->count());
    }

    public function testShuffle(): void
    {
        $originalArray = $this->collection->toArray();
        $orderChanged = false;

        for ($i = 0; $i < 100; $i++) {
            $shuffled = $this->collection->shuffle();
            if ($originalArray !== $shuffled->toArray()) {
                $orderChanged = true;
                break;
            }
        }

        $this->assertTrue($orderChanged);
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

    public function testReset(): void
    {
        $newElements = [3, 4];

        $this->assertEquals($newElements, $this->collection->reset($newElements)->toArray());
        $this->assertEquals([], $this->collection->reset([])->toArray());
    }

    public function testGetOneThrowExceptionWithMissingKey(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_MISSING_KEY);

        $this->collection->getOne(2);
    }

    public function testGetOne(): void
    {
        $element = $this->collection->getOne(0);
        $this->assertEquals($this->elements[0], $element);
    }

    public function testRemoveOneThrowExceptionWithMissingKey(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_MISSING_KEY);

        $this->collection->RemoveOne('missing-key');
    }

    public function testRemoveOne(): void
    {
        $this->collection->removeOne(0);
        $this->assertEquals([1 => $this->elements[1]], $this->collection->toArray());
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
        $collection = new \App\GameCore\GameOption\CollectionGameOption($this->handler);

        $collection->pullFirst();
    }

    public function testPullFirst(): void
    {
        $this->assertEquals(1, $this->collection->pullFirst());
    }

    public function testPullLastThrowExceptionIfCollectionEmpty(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_NO_ELEMENTS);
        $collection = new CollectionBase($this->handler);

        $collection->pullLast();
    }

    public function testPullLast(): void
    {
        $this->assertEquals(2, $this->collection->pullLast());
    }

    public function testAdd(): void
    {
        $this->assertTrue($this->collection->add(3)->exist(2));
    }
}
