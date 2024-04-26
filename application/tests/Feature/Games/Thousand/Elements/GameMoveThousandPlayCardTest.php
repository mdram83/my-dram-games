<?php

namespace Games\Thousand\Elements;

use App\GameCore\GameElements\GameMove\GameMoveException;
use App\GameCore\Player\Player;
use App\Games\Thousand\Elements\GameMoveThousandBidding;
use App\Games\Thousand\Elements\GameMoveThousandCountPoints;
use App\Games\Thousand\Elements\GameMoveThousandDeclaration;
use App\Games\Thousand\Elements\GameMoveThousandPlayCard;
use App\Games\Thousand\Elements\GameMoveThousandStockDistribution;
use App\Games\Thousand\Elements\GamePhaseThousandBidding;
use App\Games\Thousand\Elements\GamePhaseThousandCountPoints;
use App\Games\Thousand\Elements\GamePhaseThousandDeclaration;
use App\Games\Thousand\Elements\GamePhaseThousandPlayFirstCard;
use App\Games\Thousand\Elements\GamePhaseThousandStockDistribution;
use stdClass;
use Tests\TestCase;

class GameMoveThousandPlayCardTest extends TestCase
{
    private Player $player;
    private array $details = ['card' => '123'];
    private GamePhaseThousandPlayFirstCard $phase;

    public function setUp(): void
    {
        parent::setUp();
        $this->player = $this->createMock(Player::class);
        $this->phase = new GamePhaseThousandPlayFirstCard();
    }

    public function testThrowExceptionWhenPhaseMissing(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);

        new GameMoveThousandPlayCard($this->player, $this->details);
    }

    public function testThrowExceptionWhenCardMissing(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);

        new GameMoveThousandPlayCard($this->player, ['wrong' => 'structure'], $this->phase);
    }

    public function testThrowExceptionWhenCardInvalid(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);

        new GameMoveThousandPlayCard($this->player, ['card' => (new stdClass())], $this->phase);
    }

    public function testThrowExceptionWhenCardEmptyString(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);

        new GameMoveThousandPlayCard($this->player, ['card' => ''], $this->phase);
    }

    public function testThrowExceptionWhenMarriageNotBoolean(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);

        new GameMoveThousandPlayCard($this->player, array_merge($this->details, ['marriage' => 'notBool']), $this->phase);
    }

    public function testCreateGameMoveThousandDeclarationWithoutMarriage(): void
    {
        $move = new GameMoveThousandPlayCard($this->player, $this->details, $this->phase);
        $this->assertEquals(array_merge($this->details, ['phase' => $this->phase]), $move->getDetails());
    }

    public function testCreateGameMoveThousandDeclarationWithMarriage(): void
    {
        $move = new GameMoveThousandPlayCard(
            $this->player,
            array_merge($this->details, ['marriage' => true]),
            $this->phase
        );

        $this->assertEquals(
            array_merge($this->details, ['phase' => $this->phase, 'marriage' => true]),
            $move->getDetails()
        );
    }
}
