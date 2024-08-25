<?php

namespace Tests\Feature\Games\TicTacToe;

use App\GameCore\GameElements\GameMove\GameMoveAbsFactory;
use App\GameCore\GameElements\GameMove\GameMoveException;
use App\Games\TicTacToe\GameMoveAbsFactoryTicTacToe;
use App\Games\TicTacToe\GameMoveTicTacToe;
use MyDramGames\Utils\Player\Player;
use Tests\TestCase;

class GameMoveAbsFactoryTicTacToeTest extends TestCase
{
    protected GameMoveAbsFactoryTicTacToe $factory;
    protected array $params = ['fieldKey' => 1];
    protected Player $player;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new GameMoveAbsFactoryTicTacToe();
        $this->player = $this->createMock(Player::class);
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(GameMoveAbsFactory::class, $this->factory);
    }

    public function testThrowExceptionIfParamsMissingFieldKey(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);

        $this->factory->create($this->player, ['invalid-key' => 1]);
    }

    public function testThrowExceptionIfParamsMissFieldKeyValue(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);

        $this->factory->create($this->player, ['fieldKey' => null]);
    }

    public function testCreateConvertStringValueToInt(): void
    {
        $move = $this->factory->create($this->player, ['fieldKey' => '1']);
        $this->assertInstanceOf(GameMoveTicTacToe::class, $move);
    }

    public function testCreate(): void
    {
        $this->assertInstanceOf(GameMoveTicTacToe::class, $this->factory->create($this->player, $this->params));
    }
}
