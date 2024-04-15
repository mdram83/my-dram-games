<?php

namespace App\GameCore\GameElements\GameMove;

use Exception;

class GameMoveException extends Exception
{
    public const MESSAGE_INVALID_MOVE_PARAMS = 'Invalid move parameters';
    public const MESSAGE_NO_ABS_FACTORY = 'Game configuration incomplete';
}
