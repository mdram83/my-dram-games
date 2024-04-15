<?php

namespace App\GameCore\GameResult;

use Exception;

class GameResultProviderException extends Exception
{
    public const MESSAGE_INCORRECT_DATA_PARAMETER = 'Incorrect data parameter';
    public const MESSAGE_RESULTS_ALREADY_SET = 'Result already provided';
    public const MESSAGE_RESULT_NOT_SET = 'Can not generate records without game result';
    public const MESSAGE_RECORD_ALREADY_SET = 'Game records already created';
}
