<?php

namespace App\Games\Thousand\Elements;

use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameElements\GamePhase\GamePhase;

class GamePhaseThousandBidding extends GamePhaseThousand implements GamePhase
{
    public const PHASE_KEY = 'bidding';
    protected const PHASE_NAME = 'Make your bids';
    protected const PHASE_DESCRIPTION = 'Make your bidding or pass';

    public function getNextPhase(bool $lastAttempt): GamePhase
    {
        if ($lastAttempt) {
            return new GamePhaseThousandStockDistribution();
        }
        return $this;
    }
}
