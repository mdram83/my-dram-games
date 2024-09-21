<?php

use MyDramGames\Core\GameOption\Options\GameOptionAutostartGeneric;
use MyDramGames\Core\GameOption\Options\GameOptionForfeitAfterGeneric;
use MyDramGames\Core\GameOption\Options\GameOptionNumberOfPlayersGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueAutostartGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueForfeitAfterGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueNumberOfPlayersGeneric;
use MyDramGames\Games\Thousand\Extensions\Core\GameOption\Options\GameOptionThousandBarrelPointsGeneric;
use MyDramGames\Games\Thousand\Extensions\Core\GameOption\Options\GameOptionThousandNumberOfBombsGeneric;
use MyDramGames\Games\Thousand\Extensions\Core\GameOption\Values\GameOptionValueThousandBarrelPointsGeneric;
use MyDramGames\Games\Thousand\Extensions\Core\GameOption\Values\GameOptionValueThousandNumberOfBombsGeneric;

return [
    'numberOfPlayers' => [
        'option' => GameOptionNumberOfPlayersGeneric::class,
        'value' => GameOptionValueNumberOfPlayersGeneric::class
    ],
    'autostart' => [
        'option' => GameOptionAutostartGeneric::class,
        'value' => GameOptionValueAutostartGeneric::class,
    ],
    'forfeitAfter' => [
        'option' => GameOptionForfeitAfterGeneric::class,
        'value' => GameOptionValueForfeitAfterGeneric::class,
    ],
    'thousand-barrel-points' => [
        'option' => GameOptionThousandBarrelPointsGeneric::class,
        'value' => GameOptionValueThousandBarrelPointsGeneric::class,
    ],
    'thousand-number-of-bombs' => [
        'option' => GameOptionThousandNumberOfBombsGeneric::class,
        'value' => GameOptionValueThousandNumberOfBombsGeneric::class,
    ],
];
