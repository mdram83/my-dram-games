<?php

namespace App\Games\Thousand\Elements;

use App\GameCore\GameElements\GamePhase\GamePhase;

class GamePhaseThousandCountPoints extends GamePhaseThousand implements GamePhase
{
    public const PHASE_KEY = 'counting-points';
    protected const PHASE_NAME = 'Counting Points';
    protected const PHASE_DESCRIPTION = 'See result of last round and for the whole game';

    public function getNextPhase(bool $lastAttempt): GamePhase
    {
        if ($lastAttempt) {
            return new GamePhaseThousandBidding();
        }
        return $this;
    }
}
