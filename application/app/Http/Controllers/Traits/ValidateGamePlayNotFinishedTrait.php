<?php

namespace App\Http\Controllers\Traits;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllerValidationException;
use MyDramGames\Core\GamePlay\GamePlay;

trait ValidateGamePlayNotFinishedTrait
{
    /**
     * @throws ControllerValidationException
     */
    private function validateGamePlayNotFinished(GamePlay $gamePlay): void
    {
        if ($gamePlay->isFinished()) {
            throw new ControllerValidationException(Controller::MESSAGE_FINISHED);
        }
    }
}
