<?php

namespace App\Games\Thousand\Elements;

use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameElements\GamePhase\GamePhase;

// TODO write tests
class GamePhaseThousandPlaySecondCard extends GamePhaseThousand implements GamePhase
{
    public const PHASE_KEY = 'playing-second-card';
    protected const PHASE_NAME = 'Play Second Card';
    protected const PHASE_DESCRIPTION = 'Next player is playing his card now';

    public function getNextPhase(bool $lastAttempt): GamePhase
    {
        return new GamePhaseThousandPlayThirdCard();
    }

    public function getMoveResults(GameMove $move): array
    {
        // TODO: Implement getMoveResults() method.
    }
}
