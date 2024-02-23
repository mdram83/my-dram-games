<?php

namespace Tests\Unit\Games\TicTacToe;

use App\Games\TicTacToe\GameSetupTicTacToe;
use PHPUnit\Framework\TestCase;

class GameSetupTicTacToeTest extends TestCase
{
    public function testDefaults(): void
    {
        $setup = new GameSetupTicTacToe();
        $options = [
            'numberOfPlayers' => $setup->getNumberOfPlayers(),
            'autostart' => $setup->getAutostart(),
        ];
        $this->assertEquals($options, $setup->getAllOptions());
        $this->assertEquals([2], $setup->getNumberOfPlayers());
    }
}
