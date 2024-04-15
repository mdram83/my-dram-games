<?php

namespace Tests\Feature\GameCore\GameElements\GamePlayPlayers;

use App\GameCore\GameElements\GamePlayPlayers\CollectionGamePlayPlayers;
use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\Collection;
use App\GameCore\Services\Collection\CollectionBase;
use App\GameCore\Services\Collection\CollectionException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class CollectionGamePlayPlayersTest extends TestCase
{
    use RefreshDatabase;

    protected Collection $handler;
    protected CollectionGamePlayPlayers $collection;
    protected Player $playerOne;
    protected Player $playerTwo;

    public function setUp(): void
    {
        parent::setUp();
        $this->handler = App::make(Collection::class);
        $this->collection = new CollectionGamePlayPlayers($this->handler);

        $this->playerOne = User::factory()->create();
        $this->playerTwo = User::factory()->create();
    }

    public function testClassInstance(): void
    {
        $this->assertInstanceOf(Collection::class, $this->collection);
        $this->assertInstanceOf(CollectionBase::class, $this->collection);
    }

    public function testThrowExceptionWhenCreatingWithIncompatibleValues(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_INCOMPATIBLE);

        new CollectionGamePlayPlayers($this->handler, [1, 2]);
    }

    public function testThrowExceptionWhenAddingIncompatibleValue(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_INCOMPATIBLE);

        $collection = new CollectionGamePlayPlayers($this->handler);
        $collection->add(1);
    }

    public function testCreateWithPlayersArray(): void
    {
        $collection = new CollectionGamePlayPlayers($this->handler, [$this->playerOne, $this->playerTwo]);
        $this->assertEquals(2, $collection->count());
    }

    public function testAddPlayers(): void
    {
        $this->collection->add($this->playerOne);
        $this->collection->add($this->playerTwo);

        $this->assertEquals(2, $this->collection->count());
    }

    public function testThrowExceptionWhenAddingSamePlayerTwice(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_DUPLICATE);

        $this->collection->add($this->playerOne);
        $this->collection->add($this->playerOne);
    }

    public function testThrowExceptionWhenCreatingWithSamePlayerTwice(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_DUPLICATE);

        new CollectionGamePlayPlayers($this->handler, [$this->playerOne, $this->playerOne]);
    }

    public function testAddPlayerWillUsePlayerIdAsKey(): void
    {
        $this->collection->add($this->playerOne);
        $id = $this->playerOne->getId();

        $this->assertEquals($id, $this->collection->getOne($id)->getId());
    }

    public function testToArray(): void
    {
        $this->collection->add($this->playerOne);
        $this->collection->add($this->playerTwo);
        $expected = [
            $this->playerOne->getId() => $this->playerOne,
            $this->playerTwo->getId() => $this->playerTwo,
        ];

        $this->assertEquals($expected, $this->collection->toArray());
    }
}
