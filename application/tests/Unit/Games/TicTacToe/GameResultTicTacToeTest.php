<?php

namespace Tests\Unit\Games\TicTacToe;

use App\GameCore\GameResult\GameResult;
use App\GameCore\GameResult\GameResultException;
use App\Games\TicTacToe\GameResultTicTacToe;
use PHPUnit\Framework\TestCase;

class GameResultTicTacToeTest extends TestCase
{
    protected string $winnerName = 'Test Name';
    protected array $winningFields = [1, 5, 9];

    public function testInterfaceInstance(): void
    {
        $result = new GameResultTicTacToe();
        $this->assertInstanceOf(GameResult::class, $result);
    }

    public function testThrowExceptionIfWinWithoutFields(): void
    {
        $this->expectException(GameResultException::class);
        $this->expectExceptionMessage(GameResultException::MESSAGE_INCORRECT_PARAMETER);

        new GameResultTicTacToe($this->winnerName);
    }

    public function testThrowExceptionIfWinWithoutWinner(): void
    {
        $this->expectException(GameResultException::class);
        $this->expectExceptionMessage(GameResultException::MESSAGE_INCORRECT_PARAMETER);

        new GameResultTicTacToe(null, $this->winningFields);
    }

    public function testThrowExceptionIfWinFieldsAreNotThreeArrayElements(): void
    {
        $this->expectException(GameResultException::class);
        $this->expectExceptionMessage(GameResultException::MESSAGE_INCORRECT_PARAMETER);

        new GameResultTicTacToe($this->winnerName, [1, 2]);
    }

    public function testWinMessage(): void
    {
        $result = new GameResultTicTacToe($this->winnerName, $this->winningFields);
        $this->assertStringContainsString($this->winnerName, $result->getMessage());
    }

    public function testDrawMessage(): void
    {
        $result = new GameResultTicTacToe();
        $this->assertStringNotContainsString($this->winnerName, $result->getMessage());
    }

    public function testWinDetails(): void
    {
        $result = new GameResultTicTacToe($this->winnerName, $this->winningFields);
        $expected = [
            'winnerName' => $this->winnerName,
            'winningFields' => array_map(fn($field) => (string) $field, $this->winningFields),
        ];

        $this->assertEquals($expected, $result->getDetails());
    }

    public function testDrawDetails(): void
    {
        $result = new GameResultTicTacToe();
        $expected = [
            'winnerName' => null,
            'winningFields' => [],
        ];
        $this->assertEquals($expected, $result->getDetails());
    }

    public function testWinToArray(): void
    {
        $result = new GameResultTicTacToe($this->winnerName, $this->winningFields);
        $this->assertEquals($result->getDetails(), $result->toArray());
    }

    public function testDrawToArray(): void
    {
        $result = new GameResultTicTacToe();
        $this->assertEquals($result->getDetails(), $result->toArray());
    }
}
