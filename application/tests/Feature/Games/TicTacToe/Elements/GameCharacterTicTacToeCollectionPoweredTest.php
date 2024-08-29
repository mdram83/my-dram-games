<?php

namespace Tests\Feature\Games\TicTacToe\Elements;

use App\Games\TicTacToe\Elements\GameCharacterTicTacToe;
use App\Games\TicTacToe\Elements\GameCharacterTicTacToeCollectionPowered;
use App\Models\User;
use MyDramGames\Utils\Exceptions\CollectionException;
use Tests\TestCase;

class GameCharacterTicTacToeCollectionPoweredTest extends TestCase
{
    protected array $players;
    protected array $characters;

    public function setUp(): void
    {
        parent::setUp();
        $this->players = [User::factory()->create(), User::factory()->create()];
        $this->characters = [
            new GameCharacterTicTacToe('x', $this->players[0]),
            new GameCharacterTicTacToe('o', $this->players[1]),
        ];
    }

    public function testThrowExceptionWhenAddingWithDuplicatedCharacter(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_DUPLICATE);

        $collection = new GameCharacterTicTacToeCollectionPowered();
        $collection->add(new GameCharacterTicTacToe('x', $this->players[0]));
        $collection->add(new GameCharacterTicTacToe('x', $this->players[1]));
    }

    public function testCreateWithCorrectSetup(): void
    {
        $collection = new GameCharacterTicTacToeCollectionPowered(null, $this->characters);
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
        $collection = new GameCharacterTicTacToeCollectionPowered();
        $collection->add($this->characters[0]);
        $collection->add($this->characters[1]);

        $this->assertEquals(2, $collection->count());
    }
}
