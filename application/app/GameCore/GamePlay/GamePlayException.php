<?php

namespace App\GameCore\GamePlay;

class GamePlayException extends \Exception
{
    public const MESSAGE_STORAGE_INCORRECT = 'Incorrect storage configuration';
    public const MESSAGE_MISSING_PLAYERS = 'Incorrect players configuration';
}
