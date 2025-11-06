<?php

namespace App\Http\Controllers;

use Exception;

class ControllerValidationException extends Exception
{
    public const string MESSAGE_INCORRECT_INPUTS = 'Incorrect inputs';
    public const string MESSAGE_FORFEIT_AFTER_DISABLED = 'Option disabled';
    public const string MESSAGE_FORFEIT_AFTER_EARLY = 'Not yet expired';
}
