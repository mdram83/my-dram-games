<?php

namespace Tests\Feature\Games\TicTacToe\Elements;

use App\GameCore\Services\Collection\Collection;
use App\GameCore\Services\Collection\CollectionException;
use App\Games\TicTacToe\Elements\CollectionGameCharacterTicTacToe;
use App\Games\TicTacToe\Elements\GameCharacterTicTacToe;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class CollectionGameCharacterTicTacToeTest extends TestCase
{
    protected Collection $handler;
    protected array $players;
    protected array $characters;

    public function setUp(): void
    {
        parent::setUp();
        $this->handler = App::make(Collection::class);
        $this->players = [User::factory()->create(), User::factory()->create()];
        $this->characters = [
            new GameCharacterTicTacToe('x', $this->players[0]),
            new GameCharacterTicTacToe('o', $this->players[1]),
        ];
    }

    public function testThrowExceptionWhenCreatingWithDuplicatedPlayer(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_DUPLICATE);

        $duplicatedPlayers = [
            new GameCharacterTicTacToe('x', $this->players[0]),
            new GameCharacterTicTacToe('o', $this->players[0]),
        ];

        new CollectionGameCharacterTicTacToe($this->handler, $duplicatedPlayers);
    }

    public function testThrowExceptionWhenAddingWithDuplicatedPlayer(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_DUPLICATE);

        $collection = new \App\Games\TicTacToe\Elements\CollectionGameCharacterTicTacToe($this->handler);
        $collection->add(new GameCharacterTicTacToe('x', $this->players[0]));
        $collection->add(new GameCharacterTicTacToe('o', $this->players[0]));
    }

    public function testThrowExceptionWhenAddingWithDuplicatedCharacter(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_DUPLICATE);

        $collection = new CollectionGameCharacterTicTacToe($this->handler);
        $collection->add(new GameCharacterTicTacToe('x', $this->players[0]));
        $collection->add(new GameCharacterTicTacToe('x', $this->players[1]));
    }

    public function testCreateWithCorrectSetup(): void
    {
        $collection = new CollectionGameCharacterTicTacToe($this->handler, $this->characters);
        $expected = [
            $this->characters[0]->getName() => $this->characters[0]->getPlayer(),
            $this->characters[1]->getName() => $this->characters[1]->getPlayer(),
        ];
        $results = $collection->toArray();


        $this->assertEquals($expected['x']->getId(), $results['x']->getPlayer()->getId());
        $this->assertEquals($expected['o']->getId(), $results['o']->getPlayer()->getId());
    }

    public function testAddWithCorrectSetup(): void
    {
        $collection = new CollectionGameCharacterTicTacToe($this->handler);
        $collection->add($this->characters[0]);
        $collection->add($this->characters[1]);

        $this->assertEquals(2, $collection->count());
    }

}
