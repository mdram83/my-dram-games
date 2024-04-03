<?php

namespace App\Games\TicTacToe;

use App\GameCore\GameOption\CollectionGameOption;
use App\GameCore\GameOption\GameOptionAutostart;
use App\GameCore\GameOption\GameOptionForfeitAfter;
use App\GameCore\GameOption\GameOptionNumberOfPlayers;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueForfeitAfter;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\GameSetup\GameSetupBase;
use App\GameCore\Services\Collection\Collection;

class GameSetupTicTacToe extends GameSetupBase
{
    protected function setDefaults(Collection $optionsHandler): void
    {
        $numberOfPlayers = new GameOptionNumberOfPlayers(
            [GameOptionValueNumberOfPlayers::Players002],
            GameOptionValueNumberOfPlayers::Players002
        );

        $autostart = new GameOptionAutostart(
            [GameOptionValueAutostart::Enabled, GameOptionValueAutostart::Disabled],
            GameOptionValueAutostart::Disabled
        );

        $forfeitAfter = new GameOptionForfeitAfter(
            [GameOptionValueForfeitAfter::Disabled, GameOptionValueForfeitAfter::Minute],
            GameOptionValueForfeitAfter::Disabled
        );

        $this->options = new CollectionGameOption($optionsHandler, [$numberOfPlayers, $autostart, $forfeitAfter]);
    }
}
