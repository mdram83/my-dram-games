<?php

namespace Tests\Feature\GameCore\GameOptionValue;

use App\GameCore\GameOption\CollectionGameOption;
use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\GameOptionValue\GameOptionValue;
use App\GameCore\Services\Collection\Collection;
use App\GameCore\Services\Collection\CollectionBase;
use App\GameCore\Services\Collection\CollectionException;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class CollectionGameOptionValueInputTest extends TestCase
{
    protected Collection $handler;
    protected array $options;
    protected Collection $collection;

    public function setUp(): void
    {
        parent::setUp();

        $this->handler = App::make(Collection::class);

        $this->options = [
            'A' => $this->createMock(GameOptionValue::class),
            'B' => $this->createMock(GameOptionValue::class),
        ];

        $this->collection = new CollectionGameOptionValueInput($this->handler, $this->options);
    }

    protected function getExpectedToArray(): array
    {
        return $this->options;
    }

    public function testInstanceOfClasses(): void
    {
        $this->assertInstanceOf(Collection::class, $this->collection);
        $this->assertInstanceOf(CollectionBase::class, $this->collection);
        $this->assertInstanceOf(CollectionGameOptionValueInput::class, $this->collection);
    }

    public function testThrowExceptionWhenResetWithIncompatibleTypes(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_INCOMPATIBLE);

        $this->collection->reset([1, 2]);
    }

    public function testReset(): void
    {
        $optionC = $this->createMock(GameOptionValue::class);
        $optionD = $this->createMock(GameOptionValue::class);

        $newElements = ['C' => $optionC, 'D' => $optionD];

        $this->assertEquals($newElements, $this->collection->reset($newElements)->toArray());
        $this->assertEquals([], $this->collection->reset()->toArray());
    }

    public function testThrowExceptionWhenCreatingWithIncompatibleElements(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_INCOMPATIBLE);

        new CollectionGameOption($this->handler, ['incompatible-1', 'incompatible-2']);
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

        $this->collection->add($this->options['A'], 'A');
    }
}
