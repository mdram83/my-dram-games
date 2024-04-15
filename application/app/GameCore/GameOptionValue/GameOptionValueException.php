<?php

namespace App\GameCore\GameOptionValue;

use Exception;

class GameOptionValueException extends Exception
{
    public const MESSAGE_MISSING_VALUE = 'Value does not exist';
}
