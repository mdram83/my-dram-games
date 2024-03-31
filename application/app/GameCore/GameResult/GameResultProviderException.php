<?php

namespace App\GameCore\GameResult;

class GameResultProviderException extends \Exception
{
    public const MESSAGE_INCORRECT_DATA_PARAMETER = 'Incorrect data parameter';
    public const MESSAGE_RESULT_NOT_SET = 'Can not generate records without game result';
}
