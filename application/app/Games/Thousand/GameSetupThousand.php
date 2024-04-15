<?php

namespace App\Games\Thousand;

use App\GameCore\GameOption\CollectionGameOption;
use App\GameCore\GameOption\GameOptionAutostart;
use App\GameCore\GameOption\GameOptionForfeitAfter;
use App\GameCore\GameOption\GameOptionNumberOfPlayers;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueForfeitAfter;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\GameSetup\GameSetupBase;
use App\GameCore\Services\Collection\Collection;

class GameSetupThousand extends GameSetupBase
{
    protected function setDefaults(Collection $optionsHandler): void
    {
        $numberOfPlayers = new GameOptionNumberOfPlayers(
            [GameOptionValueNumberOfPlayers::Players003, GameOptionValueNumberOfPlayers::Players004],
            GameOptionValueNumberOfPlayers::Players003
        );

        $autostart = new GameOptionAutostart(
            [GameOptionValueAutostart::Enabled, GameOptionValueAutostart::Disabled],
            GameOptionValueAutostart::Disabled
        );

        $forfeitAfter = new GameOptionForfeitAfter(
            [
                GameOptionValueForfeitAfter::Disabled,
                GameOptionValueForfeitAfter::Minutes10,
                GameOptionValueForfeitAfter::Hour,
            ],
            GameOptionValueForfeitAfter::Disabled
        );

        $barrelPoints = new GameOptionThousandBarrelPoints(
            [
                GameOptionValueThousandBarrelPoints::EightHundred,
                GameOptionValueThousandBarrelPoints::NineHundred,
                GameOptionValueThousandBarrelPoints::Disabled,
            ],
            GameOptionValueThousandBarrelPoints::EightHundred
        );

        $numberOfBombs = new GameOptionThousandNumberOfBombs(
            [
                GameOptionValueThousandNumberOfBombs::One,
                GameOptionValueThousandNumberOfBombs::Two,
                GameOptionValueThousandNumberOfBombs::Disabled,
            ],
            GameOptionValueThousandNumberOfBombs::One
        );

        $reDealConditions = new GameOptionThousandReDealConditions(
            [
                GameOptionValueThousandReDealConditions::Disabled,
                GameOptionValueThousandReDealConditions::FourNines,
                GameOptionValueThousandReDealConditions::TenPoints,
            ],
            GameOptionValueThousandReDealConditions::Disabled
        );

        $this->options = new CollectionGameOption(
            $optionsHandler,
            [$numberOfPlayers, $autostart, $forfeitAfter, $barrelPoints, $numberOfBombs, $reDealConditions]
        );
    }
}
