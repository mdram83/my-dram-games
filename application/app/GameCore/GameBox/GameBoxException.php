<?php

namespace App\GameCore\GameBox;

class GameBoxException extends \Exception
{
    public const MESSAGE_GAME_DEFINITION_MISSING = 'Missing game configuration';
    public const MESSAGE_INCORRECT_CONFIGURATION = 'Incorrect game configuration';
}
