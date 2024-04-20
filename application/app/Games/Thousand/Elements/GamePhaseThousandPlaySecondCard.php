<?php

namespace App\Games\Thousand\Elements;

use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameElements\GamePhase\GamePhase;
use App\GameCore\GameElements\GamePhase\GamePhaseException;

class GamePhaseThousandPlaySecondCard extends GamePhaseThousand implements GamePhase
{
    public const PHASE_KEY = 'playing-second-card';
    protected const PHASE_NAME = 'Play Second Card';
    protected const PHASE_DESCRIPTION = 'Next player is playing his card now';

    /**
     * @throws GamePhaseException
     */
    public function getNextPhase(bool $lastAttempt): GamePhase
    {
        if (!$lastAttempt) {
            throw new GamePhaseException(GamePhaseException::MESSAGE_PHASE_SINGLE_ATTEMPT);
        }
        return new GamePhaseThousandPlayThirdCard();
    }
}
