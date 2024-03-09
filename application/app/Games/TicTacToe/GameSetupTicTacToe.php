<?php

namespace App\Games\TicTacToe;

use App\GameCore\GameOption\GameOptionAutostart;
use App\GameCore\GameOption\GameOptionNumberOfPlayers;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\GameSetup\GameSetupBase;

class GameSetupTicTacToe extends GameSetupBase
{
    protected function setDefaults(): void
    {
        $numberOfPlayers = new GameOptionNumberOfPlayers(
            [GameOptionValueNumberOfPlayers::Players002],
            GameOptionValueNumberOfPlayers::Players002
        );

        $autostart = new GameOptionAutostart(
            [GameOptionValueAutostart::Enabled, GameOptionValueAutostart::Disabled],
            GameOptionValueAutostart::Disabled
        );

        $this->options = [
            $numberOfPlayers->getKey() => $numberOfPlayers,
            $autostart->getKey() => $autostart,
        ];
    }
}
