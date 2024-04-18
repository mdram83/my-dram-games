<?php

namespace App\Games\Thousand\Elements;

use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameElements\GamePhase\GamePhase;

// TODO write tests
class GamePhaseThousandPlayFirstCard extends GamePhaseThousand implements GamePhase
{
    public const PHASE_KEY = 'playing-first-card';
    protected const PHASE_NAME = 'Play First Card';
    protected const PHASE_DESCRIPTION = 'Bidding or last trick winner play first card';

    public function getNextPhase(bool $lastAttempt): GamePhase
    {
        return new GamePhaseThousandPlaySecondCard();
    }

    public function getMoveResults(GameMove $move): array
    {
        // TODO: Implement getMoveResults() method.
    }
}
