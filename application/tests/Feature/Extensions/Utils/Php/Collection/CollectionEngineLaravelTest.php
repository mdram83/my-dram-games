<?php

namespace Tests\Feature\Extensions\Utils\Php\Collection;

use App\Extensions\Utils\Php\Collection\CollectionEngineLaravel;
use MyDramGames\Utils\Exceptions\CollectionException;
use Tests\TestCase;

class CollectionEngineLaravelTest extends TestCase
{
    private CollectionEngineLaravel $collectionEmpty;
    private CollectionEngineLaravel $collection;
    private array $items;

    public function setUp(): void
    {
        parent::setUp();
        $this->items = ['A' => 1, 'B' => 2, 'C' => 3];
        $this->collectionEmpty = new CollectionEngineLaravel();
        $this->collection = new CollectionEngineLaravel($this->items);
    }

    public function testCount(): void
    {
        $this->assertEquals(0, $this->collectionEmpty->count());
        $this->assertEquals(count($this->items), $this->collection->count());
    }

    public function testIsEmpty(): void
    {
        $this->assertTrue($this->collectionEmpty->isEmpty());
        $this->assertFalse($this->collection->isEmpty());
    }

    public function testExist(): void
    {
        $this->assertTrue($this->collection->exist(array_keys($this->items)[0]));
        $this->assertFalse($this->collection->exist('definitely-missing-elements-key-AB987'));
    }

    public function testToArray(): void
    {
        $itemsAdd = array_merge($this->items, [4]);
        $collectionAdd = new CollectionEngineLaravel($itemsAdd);

        $this->assertSame($this->items, $this->collection->toArray());
        $this->assertSame(array_values($itemsAdd), array_values($collectionAdd->toArray()));
        $this->assertSame([], $this->collectionEmpty->toArray());
    }

    public function testEach(): void
    {
        $callback = fn($item) => $item * 2;
        $callbackItems = array_map($callback, $this->items);
        $callbackCollection = $this->collection->each($callback);

        $this->assertSame($callbackItems, $callbackCollection->toArray());
        $this->assertSame($this->collection->toArray(), $callbackCollection->toArray());
        $this->assertSame([], $this->collectionEmpty->each($callback)->toArray());
    }

    public function testFilter(): void
    {
        $callbackValue = fn($item) => $item > 1;
        $callbackValueItems = array_filter($this->items, $callbackValue);
        $callbackValueCollection = $this->collection->filter($callbackValue);

        $this->assertSame($callbackValueItems, $callbackValueCollection->toArray());
        $this->assertNotSame($this->collection->toArray(), $callbackValueCollection->toArray());
        $this->assertSame([], $this->collectionEmpty->filter($callbackValue)->toArray());
    }

    public function testShuffle(): void
    {
        $shuffled = false;
        for ($i = 0; $i < 100; $i++) {
            $shuffledCollection = $this->collection->shuffle();
            if (array_values($this->collection->toArray()) !== array_values($this->items)) {
                $shuffled = true;
                break;
            }
        }

        $this->collectionEmpty->shuffle();

        $this->assertTrue($shuffled);
        $this->assertSame($shuffledCollection->toArray(), $this->collection->toArray());
        $this->assertTrue($this->collection->exist(array_keys($this->items)[0]));
        $this->assertSame([], $this->collectionEmpty->toArray());
    }

    public function testRandomThrowExceptionIfCollectionEmpty(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_NO_ELEMENTS);

        $this->collectionEmpty->random();
    }

    public function testRandom(): void
    {
        $different = false;
        $exists = true;

        for ($i = 0; $i < 100; $i++) {

            if (!in_array($this->collection->random(), $this->items, true)) {
                $exists = false;
                break;
            }

            if ($this->collection->random() !== $this->collection->random()) {
                $different = true;
                break;
            }
        }

        $this->assertTrue($exists);
        $this->assertTrue($different);
    }

    public function testReset(): void
    {
        $this->collection->reset(['A' => 1, 'B' => 2]);
        $this->assertEquals(['A' => 1, 'B' => 2], $this->collection->toArray());
    }

    public function testAddThrowExceptionForDuplicatedKey(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_DUPLICATE);

        $key = array_keys($this->items)[0];
        $this->collection->add($this->items[$key], $key);
    }

    public function testAdd(): void
    {
        $this->assertTrue($this->collection->add(4, 'D')->exist('D'));
        $this->assertEquals(5, $this->collection->add(5)->count());
        $this->assertEquals(6, $this->collection->add(6, 1)->count());
    }

    public function testGetOneThrowExceptionWithMissingKey(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_MISSING_KEY);

        $this->collection->getOne('definitely-missing-key-AC(*&S');
    }

    public function testGetOne(): void
    {
        $key = array_keys($this->items)[0];
        $this->assertSame($this->items[$key], $this->collection->getOne($key));
    }

    public function testRemoveOneThrowExceptionWithMissingKey(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_MISSING_KEY);

        $this->collection->RemoveOne('definitely-missing-key-AC(*&S');
    }

    public function testRemoveOne(): void
    {
        $key = array_keys($this->items)[0];
        $this->collection->removeOne($key);

        $this->assertFalse($this->collection->exist($key));
        $this->assertEquals(count($this->items) - 1, $this->collection->count());
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

        $this->collectionEmpty->pullFirst();
    }

    public function testPullFirst(): void
    {
        $this->assertEquals(1, $this->collection->pullFirst());
        $this->assertEquals(['B' => 2, 'C' => 3], $this->collection->toArray());
    }

    public function testPullLastThrowExceptionIfCollectionEmpty(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_NO_ELEMENTS);

        $this->collectionEmpty->pullLast();
    }

    public function testPullLast(): void
    {
        $this->assertEquals(3, $this->collection->pullLast());
        $this->assertEquals(['A' => 1, 'B' => 2], $this->collection->toArray());
    }

    public function testClone(): void
    {
        $clone = $this->collection->clone();
        $clone->reset();

        $this->assertEquals(0, $clone->count());
        $this->assertEquals(count($this->items), $this->collection->count());
    }

    public function testKeys(): void
    {
        $this->assertSame(array_keys($this->items), $this->collection->keys());
        $this->assertSame([], $this->collectionEmpty->keys());
    }

    public function testGetManyThrowExceptionForNotFlatArray(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_KEYS_INPUTS);

        $incompatibleInputStructure = ['A', ['array-not-expected']];
        $this->collection->getMany($incompatibleInputStructure);
    }

    public function testGetManyThrowExceptionForMissingElementWithSpecificKey(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_MISSING_KEY);

        $keys = [array_keys($this->items)[0], 'definitely-missing-key-AC(*&S'];
        $this->collection->getMany($keys);
    }

    public function testGetMany(): void
    {
        $allKeys = array_keys($this->items);
        $requestedKeys = [$allKeys[0], $allKeys[1]];
        $requestedCollection = $this->collection->getMany($requestedKeys);

        $this->assertSame($requestedKeys, $requestedCollection->keys());
        $this->assertEquals(count($this->items), $this->collection->count());
    }

    public function testSortKeys(): void
    {
        $initialKeys = array_keys($this->items);
        $orderedKeys = [$initialKeys[1], $initialKeys[2], $initialKeys[0]];
        $this->collection->sortKeys(function($keyOne, $keyTwo) use ($orderedKeys): int {
            return array_search($keyOne, $orderedKeys) > array_search($keyTwo, $orderedKeys) ? 1 : 0;
        });

        $this->assertSame($orderedKeys, $this->collection->keys());
    }

    public function testPullThrowExceptionForMissingKey(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_MISSING_KEY);

        $this->collection->pull('definitely-this-is-123-missing-k3yKHGGS*');
    }

    public function testPull(): void
    {
        $key = array_keys($this->items)[0];
        $pulled = $this->collection->pull($key);

        $this->assertSame($this->items[$key], $pulled);
        $this->assertEquals(count($this->items) - 1, $this->collection->count());
    }

    public function testGetNthThrowExceptionWhenEmpty(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_NO_ELEMENTS);

        $collection = new CollectionEngineLaravel();
        $collection->getNth(0);
    }

    public function testGetNthThrowExceptionWhenNotEnoughElements(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_NOT_ENOUGH);

        $this->collection->getNth($this->collection->count());
    }

    public function testGetNth(): void
    {
        $elements = ['A' => 2, 'Z' => 10, 'C' => 6];
        $this->collection->reset($elements);

        $this->assertEquals(2, $this->collection->getNth(0));
        $this->assertEquals(10, $this->collection->getNth(1));
        $this->assertEquals(6, $this->collection->getNth(2));
    }
}
