<?php

use MyDramGames\Core\GameOption\Options\GameOptionAutostartGeneric;
use MyDramGames\Core\GameOption\Options\GameOptionForfeitAfterGeneric;
use MyDramGames\Core\GameOption\Options\GameOptionNumberOfPlayersGeneric;
use MyDramGames\Games\Thousand\Extensions\Core\GameOption\Options\GameOptionThousandBarrelPointsGeneric;
use MyDramGames\Games\Thousand\Extensions\Core\GameOption\Options\GameOptionThousandNumberOfBombsGeneric;

return [
    'numberOfPlayers' => GameOptionNumberOfPlayersGeneric::class,
    'autostart' => GameOptionAutostartGeneric::class,
    'forfeitAfter' => GameOptionForfeitAfterGeneric::class,
    'thousand-barrel-points' => GameOptionThousandBarrelPointsGeneric::class,
    'thousand-number-of-bombs' => GameOptionThousandNumberOfBombsGeneric::class,
];
