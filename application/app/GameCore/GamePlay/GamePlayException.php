<?php

namespace App\GameCore\GamePlay;

class GamePlayException extends \Exception
{
    public const MESSAGE_STORAGE_INCORRECT = 'Incorrect storage configuration';
    public const MESSAGE_MISSING_PLAYERS = 'Incorrect players configuration';
    public const MESSAGE_NO_ABS_FACTORY = 'Game configuration incomplete';
    public const MESSAGE_NO_ABS_CLASS = 'Game configuration incomplete';

    public const MESSAGE_NOT_CURRENT_PLAYER = 'It is not Player move now';
    public const MESSAGE_INCOMPATIBLE_MOVE = 'Move configuration incompatible with game';
    public const MESSAGE_MOVE_ON_FINISHED_GAME = 'Can not make move on finished game';
}
