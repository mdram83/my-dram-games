<?php

namespace App\Games\Thousand\Elements;

use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameElements\GamePhase\GamePhase;
use App\GameCore\GameElements\GamePhase\GamePhaseException;

class GamePhaseThousandPlayFirstCard extends GamePhaseThousand implements GamePhase
{
    public const PHASE_KEY = 'playing-first-card';
    protected const PHASE_NAME = 'Play First Card';
    protected const PHASE_DESCRIPTION = 'Bidding or last trick winner play first card';

    /**
     * @throws GamePhaseException
     */
    public function getNextPhase(bool $lastAttempt): GamePhase
    {
        if (!$lastAttempt) {
            throw new GamePhaseException(GamePhaseException::MESSAGE_PHASE_SINGLE_ATTEMPT);
        }
        return new GamePhaseThousandPlaySecondCard();
    }
}
