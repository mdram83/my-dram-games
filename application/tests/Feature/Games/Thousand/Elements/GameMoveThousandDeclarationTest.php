<?php

namespace Tests\Feature\Games\Thousand\Elements;

use App\GameCore\GameElements\GameMove\GameMoveException;
use App\Games\Thousand\Elements\GameMoveThousandDeclaration;
use App\Games\Thousand\Elements\GamePhaseThousandDeclaration;
use MyDramGames\Utils\Player\Player;
use Tests\TestCase;

class GameMoveThousandDeclarationTest extends TestCase
{
    private Player $player;
    private array $details = ['declaration' => 200];
    private GamePhaseThousandDeclaration $phase;

    public function setUp(): void
    {
        parent::setUp();
        $this->player = $this->createMock(Player::class);
        $this->phase = new GamePhaseThousandDeclaration();
    }

    public function testThrowExceptionWhenPhaseMissing(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);

        new GameMoveThousandDeclaration($this->player, $this->details);
    }

    public function testThrowExceptionWhenDeclarationMissing(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);

        new GameMoveThousandDeclaration($this->player, ['wrong' => 'structure'], $this->phase);
    }

    public function testThrowExceptionWhenDeclarationInvalid(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);

        new GameMoveThousandDeclaration($this->player, ['declaration' => 'invalid'], $this->phase);
    }

    public function testCreateGameMoveThousandDeclaration(): void
    {
        $move = new GameMoveThousandDeclaration($this->player, $this->details, $this->phase);
        $this->assertEquals(array_merge($this->details, ['phase' => $this->phase]), $move->getDetails());
    }

    public function testCreateGameMoveThousandDeclarationForBomb(): void
    {
        $move = new GameMoveThousandDeclaration($this->player, ['declaration' => 0], $this->phase);
        $this->assertEquals(array_merge(['declaration' => 0], ['phase' => $this->phase]), $move->getDetails());
    }
}
