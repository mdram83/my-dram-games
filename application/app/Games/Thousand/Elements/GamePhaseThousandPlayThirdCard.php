<?php

namespace App\Games\Thousand\Elements;

use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameElements\GamePhase\GamePhase;

// TODO write tests
class GamePhaseThousandPlayThirdCard extends GamePhaseThousand implements GamePhase
{
    protected const PHASE_KEY = 'playing-third-card';
    protected const PHASE_NAME = 'Play Third Card';
    protected const PHASE_DESCRIPTION = 'Last player is playing his card now';

    public function getNextPhase(bool $lastAttempt): GamePhase
    {
        if ($lastAttempt) {
            return new GamePhaseThousandCountPoints();
        }
        return new GamePhaseThousandPlayFirstCard();
    }

    public function getMoveResults(GameMove $move): array
    {
        // TODO: Implement getMoveResults() method.
    }
}
