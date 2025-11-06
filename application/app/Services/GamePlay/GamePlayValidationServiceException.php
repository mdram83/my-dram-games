<?php

namespace App\Services\GamePlay;

use Exception;

class GamePlayValidationServiceException extends Exception
{
    public const string MESSAGE_FINISHED = 'Game already finished';
}
