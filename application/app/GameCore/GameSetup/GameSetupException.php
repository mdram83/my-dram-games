<?php

namespace App\GameCore\GameSetup;

class GameSetupException extends \Exception
{
    public const MESSAGE_OPTION_NOT_SET = 'Required option not set';
    public const MESSAGE_OPTION_INCORRECT = 'Options incorrectly set';
    public const MESSAGE_OPTION_OUTSIDE = 'Option(s) is/are exceeding game available values';
    public const MESSAGE_NO_ABS_FACTORY = 'Game configuration incomplete';
}
