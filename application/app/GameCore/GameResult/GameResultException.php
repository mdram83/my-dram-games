<?php

namespace App\GameCore\GameResult;

use Exception;

class GameResultException extends Exception
{
    public const MESSAGE_INCORRECT_PARAMETER = 'Incorrect input parameters';
}
