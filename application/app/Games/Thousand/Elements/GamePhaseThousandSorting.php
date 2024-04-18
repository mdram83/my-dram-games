<?php

namespace App\Games\Thousand\Elements;

use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameElements\GamePhase\GamePhase;

// TODO write tests
class GamePhaseThousandSorting extends GamePhaseThousand implements GamePhase
{
    public const PHASE_KEY = 'sorting';
    protected const PHASE_NAME = 'Sorting Hand';
    protected const PHASE_DESCRIPTION = 'Sort cards on your hand and get ready for bidding';

    public function getNextPhase(bool $lastAttempt): GamePhase
    {
        if ($lastAttempt) {
            return new GamePhaseThousandBidding();
        }
        return $this;
    }

    public function getMoveResults(GameMove $move): array
    {
        // TODO: Implement getMoveResults() method.
    }
}
