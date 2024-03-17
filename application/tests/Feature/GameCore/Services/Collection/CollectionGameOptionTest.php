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
        $optionA->method('isConfigured')->willReturn(true);

        $optionB = $this->createMock(GameOption::class);
        $optionB->method('getKey')->willReturn('B');
        $optionB->method('isConfigured')->willReturn(false);

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
        $this->assertInstanceOf(CollectionGameOption::class, $this->collection);
    }

    public function testFilter(): void
    {
        $this->assertEquals(1, $this->collection->filter(fn($option) => $option->isConfigured())->count());
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
}
