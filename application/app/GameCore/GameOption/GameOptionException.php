<?php

namespace App\GameCore\GameOption;

use Exception;

class GameOptionException extends Exception
{
    public const MESSAGE_INCORRECT_AVAILABLE = 'Incorrect available options';
    public const MESSAGE_INCORRECT_DEFAULT = 'Incorrect default option';
    public const MESSAGE_NOT_CONFIGURED = 'Game option not configured yet';
    public const MESSAGE_ALREADY_CONFIGURED = 'Game option already configured';
    public const MESSAGE_OPTION_NOT_EXIST = 'Game option does not exist';
}
