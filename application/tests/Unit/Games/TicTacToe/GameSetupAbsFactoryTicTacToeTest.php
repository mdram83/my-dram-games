<?php

namespace Tests\Unit\Games\TicTacToe;

use App\GameCore\GameSetup\GameSetupAbsFactory;
use App\Games\TicTacToe\GameSetupAbsFactoryTicTacToe;
use App\Games\TicTacToe\GameSetupTicTacToe;
use PHPUnit\Framework\TestCase;

class GameSetupAbsFactoryTicTacToeTest extends TestCase
{
    public function testInstanceOfGameSetupAbsFactory(): void
    {
        $this->assertInstanceOf(GameSetupAbsFactory::class, new GameSetupAbsFactoryTicTacToe());
    }

    public function testCreateWithoutOptions(): void
    {
        $factory = new GameSetupAbsFactoryTicTacToe();

        $this->assertInstanceOf(GameSetupTicTacToe::class, $factory->create());
    }
}
