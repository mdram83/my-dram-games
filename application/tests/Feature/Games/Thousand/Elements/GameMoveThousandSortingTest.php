<?php

namespace Games\Thousand\Elements;

use App\GameCore\GameElements\GameMove\GameMoveException;
use App\GameCore\Player\Player;
use App\Games\Thousand\Elements\GameMoveThousand;
use App\Games\Thousand\Elements\GameMoveThousandSorting;
use App\Games\Thousand\Elements\GamePhaseThousand;
use Tests\TestCase;

class GameMoveThousandSortingTest extends TestCase
{
    private Player $player;
    private array $details = ['A-H', '123', 'Q-D'];

    public function setUp(): void
    {
        parent::setUp();
        $this->player = $this->createMock(Player::class);
        $this->player->method('getId')->willReturn('12345abcde');
    }

    public function testInterface(): void
    {
        $move = new GameMoveThousandSorting($this->player, $this->details);
        $this->assertInstanceOf(GameMoveThousand::class, $move);
    }

    public function testThrowExceptionWhenPassingPhaseToSortingConstructur(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);

        new GameMoveThousandSorting($this->player, $this->details, $this->createMock(GamePhaseThousand::class));
    }

    public function testThrowExceptionWhenDetailsIsNotArrayOfStrings(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);

        $details = [123, $this->player, '123'];
        new GameMoveThousandSorting($this->player, $details);
    }

    public function testThrowExceptionWhenDetailsHasMoreThanElevenetElements(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);

        $details = array_fill(0, 12, 'A-H');
        new GameMoveThousandSorting($this->player, $details);
    }

    public function testThrowExceptionWhenDetailsHasNoElements(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);

        $details = [];
        new GameMoveThousandSorting($this->player, $details);
    }

    public function testThrowExceptionWhenDetailsHasSingleElement(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);

        $details = ['123'];
        new GameMoveThousandSorting($this->player, $details);
    }

    public function testGetPlayer(): void
    {
        $move = new GameMoveThousandSorting($this->player, $this->details);
        $this->assertEquals($this->player->getId(), $move->getPlayer()->getId());
    }

    public function testGetDetails(): void
    {
        $move = new GameMoveThousandSorting($this->player, $this->details);
        $expected = array_merge($this->details, ['phase' => null]);

        $this->assertEquals($expected, $move->getDetails());
    }
}
