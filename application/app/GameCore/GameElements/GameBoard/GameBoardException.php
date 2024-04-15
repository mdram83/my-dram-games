<?php

namespace App\GameCore\GameElements\GameBoard;

use Exception;

class GameBoardException extends Exception
{
    public const MESSAGE_INVALID_FIELD_VALUE = 'Invalid field value';
    public const MESSAGE_INVALID_FIELD_ID = 'Invalid field id';
    public const MESSAGE_FIELD_ALREADY_SET = 'Field value already set';
}
