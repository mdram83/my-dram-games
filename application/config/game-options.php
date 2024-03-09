<?php

use App\GameCore\GameOption\GameOptionAutostart;
use App\GameCore\GameOption\GameOptionNumberOfPlayers;

return [
    'numberOfPlayers' => GameOptionNumberOfPlayers::class,
    'autostart' => GameOptionAutostart::class,
];
