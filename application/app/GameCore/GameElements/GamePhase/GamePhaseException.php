<?php

namespace App\GameCore\GameElements\GamePhase;

use Exception;

class GamePhaseException extends Exception
{
    public const MESSAGE_INCORRECT_KEY = 'Incorrect key';
    public const MESSAGE_PHASE_SINGLE_ATTEMPT = 'Phase can has only final attempt';
}
