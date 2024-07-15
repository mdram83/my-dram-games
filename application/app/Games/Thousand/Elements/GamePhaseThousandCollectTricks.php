<?php

namespace App\Games\Thousand\Elements;

use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameElements\GamePhase\GamePhase;
use App\GameCore\GameElements\GamePhase\GamePhaseException;

class GamePhaseThousandCollectTricks extends GamePhaseThousand implements GamePhase
{
    public const PHASE_KEY = 'collecting-tricks';
    protected const PHASE_NAME = 'Collect Tricks';
    protected const PHASE_DESCRIPTION = 'Trick winner pick the trick from the table-';

    public function getNextPhase(bool $lastAttempt): GamePhase
    {
        if ($lastAttempt) {
            return new GamePhaseThousandCountPoints();
        }
        return new GamePhaseThousandPlayFirstCard();
    }
}
