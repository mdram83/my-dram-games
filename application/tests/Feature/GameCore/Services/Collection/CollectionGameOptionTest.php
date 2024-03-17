<?php

namespace Tests\Feature\GameCore\Services\Collection;

use App\GameCore\GameOption\GameOption;
use App\GameCore\Services\Collection\Collection;
use App\GameCore\Services\Collection\CollectionBase;
use App\GameCore\Services\Collection\CollectionException;
use App\GameCore\Services\Collection\CollectionGameOption;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class CollectionGameOptionTest extends TestCase
{
    protected Collection $handler;
    protected array $options;
    protected Collection $collection;

    public function setUp(): void
    {
        parent::setUp();

        $optionA = $this->createMock(GameOption::class);
        $optionA->method('getKey')->willReturn('A');
        $optionB = $this->createMock(GameOption::class);
        $optionB->method('getKey')->willReturn('B');

        $this->handler = App::make(Collection::class);
        $this->options = [$optionA, $optionB];
        $this->collection = new CollectionGameOption($this->handler, $this->options);
    }

    protected function getExpectedToArray(): array
    {
        return [
            $this->options[0]->getKey() => $this->options[0],
            $this->options[1]->getKey() => $this->options[1],
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
        $this->assertTrue((new CollectionGameOption($this->handler))->isEmpty());
    }

    public function testExist(): void
    {
        $this->assertFalse($this->collection->exist('missing-key'));
        $this->assertTrue($this->collection->exist('A'));
        $this->assertTrue($this->collection->exist('B'));
    }

    public function testToArray(): void
    {
        $this->assertEquals($this->getExpectedToArray(), $this->collection->toArray());
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
            if ($initial->getKey() !== $this->collection->random()->getKey()) {
                $updated = true;
                break;
            }
        }

        $this->assertTrue($updated);
    }

    public function testThrowExceptionWhenResetWithIncompatibleTypes(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_INCOMPATIBLE);

        $this->collection->reset([1, 2]);
    }

    public function testThrowExceptionWhenResetWithDuplicates(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_DUPLICATE);

        $this->collection->reset([$this->options[0], $this->options[0]]);
    }

    public function testReset(): void
    {
        $optionC = $this->createMock(GameOption::class);
        $optionC->method('getKey')->willReturn('C');

        $optionD = $this->createMock(GameOption::class);
        $optionD->method('getKey')->willReturn('D');

        $newElements = ['C' => $optionC, 'D' => $optionD];

        $this->assertEquals($newElements, $this->collection->reset($newElements)->toArray());
        $this->assertEquals([], $this->collection->reset([])->toArray());
    }

    public function testGetOneThrowExceptionWithMissingKey(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_MISSING_KEY);

        $this->collection->getOne('C');
    }

    public function testGetOne(): void
    {
        $option = $this->collection->getOne('A');
        $this->assertEquals('A', $option->getKey());
        $this->assertInstanceOf(GameOption::class, $option);
    }

    public function testRemoveOneThrowExceptionWithMissingKey(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_MISSING_KEY);

        $this->collection->RemoveOne('C');
    }

    public function testRemoveOne(): void
    {
        $this->collection->removeOne('A');
        $this->assertEquals(['B' => $this->options[1]], $this->collection->toArray());
        $this->assertFalse($this->collection->exist('A'));
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
        $collection = new CollectionGameOption($this->handler);

        $collection->pullFirst();
    }

    public function testPullFirst(): void
    {
        $this->assertEquals('A', $this->collection->pullFirst()->getKey());
    }

    public function testPullLastThrowExceptionIfCollectionEmpty(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_NO_ELEMENTS);
        $collection = new CollectionGameOption($this->handler);

        $collection->pullLast();
    }

    public function testPullLast(): void
    {
        $this->assertEquals('B', $this->collection->pullLast()->getKey());
    }

    public function testThrowExceptionWhenCreatingWithIncompatibleElements(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_INCOMPATIBLE);

        new CollectionGameOption($this->handler, ['incompatible-1', 'incompatible-2']);
    }

    public function testThrowExceptionWhenCeatingWithDuplicatedKeys(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_DUPLICATE);

        new CollectionGameOption($this->handler, [$this->options[0], $this->options[0]]);
    }

    public function testThrowExceptionWhenAddingIncompatibleType(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_INCOMPATIBLE);

        $this->collection->add(1);
    }

    public function testThrowExceptionWhenAddingExistingKey(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_DUPLICATE);

        $this->collection->add($this->options[0]);
    }

    public function testAdd(): void
    {
        $optionC = $this->createMock(GameOption::class);
        $optionC->method('getKey')->willReturn('C');

        $this->assertTrue($this->collection->add($optionC)->exist('C'));
    }
}
