<?php

namespace App\Http\Controllers\Traits;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllerException;
use MyDramGames\Core\GamePlay\GamePlay;

trait ValidateGamePlayNotFinishedTrait
{
    /**
     * @throws ControllerException
     */
    private function validateGamePlayNotFinished(GamePlay $gamePlay): void
    {
        if ($gamePlay->isFinished()) {
            throw new ControllerException(Controller::MESSAGE_FINISHED);
        }
    }
}
