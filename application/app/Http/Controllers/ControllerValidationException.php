<?php

namespace App\Http\Controllers;

use Exception;

class ControllerValidationException extends Exception
{
    public const string MESSAGE_FINISHED = 'Game already finished';
    public const string MESSAGE_INCORRECT_INPUTS = 'Incorrect inputs';
}
