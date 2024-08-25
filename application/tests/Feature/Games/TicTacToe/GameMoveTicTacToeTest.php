<?php

namespace Tests\Feature\Games\TicTacToe;

use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameElements\GameMove\GameMoveException;
use App\Games\TicTacToe\GameMoveTicTacToe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MyDramGames\Utils\Player\Player;
use Tests\TestCase;

class GameMoveTicTacToeTest extends TestCase
{
    use RefreshDatabase;

    protected GameMoveTicTacToe $move;
    protected Player $player;
    protected int $fieldKey = 1;

    public function setUp(): void
    {
        parent::setUp();
        $this->player = User::factory()->create();
        $this->move = $this->createMove();
    }

    protected function createMove(int $overwriteKey = null): GameMoveTicTacToe
    {
        return new GameMoveTicTacToe($this->player, $overwriteKey ?? $this->fieldKey);
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(GameMove::class, $this->move);
    }

    public function testThrowExceptionWithInvalidFieldKey(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);

        $this->move = $this->createMove(10);
    }

    public function testGetPlayer(): void
    {
        $this->assertSame($this->player, $this->move->getPlayer());
    }

    public function testGetDetails(): void
    {
        $expected = ['fieldKey' => $this->fieldKey];
        $this->assertEquals($expected, $this->move->getDetails());
    }
}
