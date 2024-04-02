<?php

namespace Tests\Feature\Games\TicTacToe\Elements;

use App\GameCore\GameElements\GameCharacter\GameCharacterException;
use App\GameCore\Player\Player;
use App\Games\TicTacToe\Elements\GameCharacterTicTacToe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameCharacterTicTacToeTest extends TestCase
{
    use RefreshDatabase;

    protected Player $player;

    public function setUp(): void
    {
        parent::setUp();
        $this->player = User::factory()->create();
    }

    public function testThrowExceptionWhenCreatingWithWrongName(): void
    {
        $this->expectException(GameCharacterException::class);
        $this->expectExceptionMessage(GameCharacterException::MESSAGE_WRONG_NAME);

        new GameCharacterTicTacToe('1', $this->player);
    }

    public function testGetXName(): void
    {
        $character = new GameCharacterTicTacToe('x', $this->player);
        $this->assertEquals('x', $character->getName());
    }

    public function testGetOName(): void
    {
        $character = new GameCharacterTicTacToe('o', $this->player);
        $this->assertEquals('o', $character->getName());
    }

    public function testGetPlayer(): void
    {
        $character = new GameCharacterTicTacToe('o', $this->player);
        $this->assertSame($this->player, $character->getPlayer());
    }
}
