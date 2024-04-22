<?php

namespace Games\Thousand\Elements;

use App\GameCore\GameElements\GameMove\GameMoveException;
use App\GameCore\Player\Player;
use App\Games\Thousand\Elements\GameMoveThousandBidding;
use App\Games\Thousand\Elements\GameMoveThousandCountPoints;
use App\Games\Thousand\Elements\GameMoveThousandStockDistribution;
use App\Games\Thousand\Elements\GamePhaseThousandBidding;
use App\Games\Thousand\Elements\GamePhaseThousandCountPoints;
use App\Games\Thousand\Elements\GamePhaseThousandStockDistribution;
use Tests\TestCase;

class GameMoveThousandStockDistributionTest extends TestCase
{
    private Player $player;
    private array $details = ['distribution' => ['playerA' => '123', 'playerB' => '234']];
    private GamePhaseThousandStockDistribution $phase;

    public function setUp(): void
    {
        parent::setUp();
        $this->player = $this->createMock(Player::class);
        $this->phase = new GamePhaseThousandStockDistribution();
    }

    public function testThrowExceptionWhenPhaseMissing(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);

        new GameMoveThousandStockDistribution($this->player, $this->details);
    }

    public function testThrowExceptionWhenWrongDataStructure(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);

        new GameMoveThousandStockDistribution($this->player, ['wrong' => 'structure'], $this->phase);
    }

    public function testThrowExceptionWhenJustOnePlayer(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);

        new GameMoveThousandStockDistribution($this->player, ['distribution' => ['player1' => '123']], $this->phase);
    }

    public function testThrowExceptionWhenMissingCard(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);

        new GameMoveThousandStockDistribution($this->player, ['distribution' => ['player1' => '123', 'player2' => '']], $this->phase);
    }

    public function testThrowExceptionWhenCardNotString(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);

        new GameMoveThousandStockDistribution($this->player, ['distribution' => ['player1' => 123, 'player2' => '123']], $this->phase);
    }

    public function testCreateGameMoveThousandStockDistribution(): void
    {
        $move = new GameMoveThousandStockDistribution($this->player, $this->details, $this->phase);
        $this->assertEquals(array_merge($this->details, ['phase' => $this->phase]), $move->getDetails());
    }
}
