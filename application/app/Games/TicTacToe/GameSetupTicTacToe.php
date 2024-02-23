<?php

namespace App\Games\TicTacToe;

use App\GameCore\GameSetup\GameSetupBase;

class GameSetupTicTacToe extends GameSetupBase
{
    protected function setDefaults(): void
    {
        $this->options = [
            'numberOfPlayers' => [2],
            'autostart' => [false],
        ];
    }
}
