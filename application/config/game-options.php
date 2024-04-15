<?php

use App\GameCore\GameOption\GameOptionAutostart;
use App\GameCore\GameOption\GameOptionForfeitAfter;
use App\GameCore\GameOption\GameOptionNumberOfPlayers;
use App\Games\Thousand\GameOptionThousandBarrelPoints;
use App\Games\Thousand\GameOptionThousandNumberOfBombs;
use App\Games\Thousand\GameOptionThousandReDealConditions;

return [
    'numberOfPlayers' => GameOptionNumberOfPlayers::class,
    'autostart' => GameOptionAutostart::class,
    'forfeitAfter' => GameOptionForfeitAfter::class,
    'thousand-barrel-points' => GameOptionThousandBarrelPoints::class,
    'thousand-number-of-bombs' => GameOptionThousandNumberOfBombs::class,
    'thousand-re-deal-conditions' => GameOptionThousandReDealConditions::class,
];
