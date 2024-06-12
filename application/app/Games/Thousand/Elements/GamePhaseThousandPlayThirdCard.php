<?php

namespace App\Games\Thousand\Elements;

use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameElements\GamePhase\GamePhase;
use App\GameCore\GameElements\GamePhase\GamePhaseException;

class GamePhaseThousandPlayThirdCard extends GamePhaseThousand implements GamePhase
{
    public const PHASE_KEY = 'playing-third-card';
    protected const PHASE_NAME = 'Play Third Card';
    protected const PHASE_DESCRIPTION = 'Last player is playing his card now';

    /**
     * @throws GamePhaseException
     */
    public function getNextPhase(bool $lastAttempt): GamePhase
    {
        if (!$lastAttempt) {
            throw new GamePhaseException(GamePhaseException::MESSAGE_PHASE_SINGLE_ATTEMPT);
        }
        return new GamePhaseThousandCollectTricks();
    }
}
