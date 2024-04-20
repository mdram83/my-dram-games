<?php

namespace App\Games\Thousand\Elements;

use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameElements\GamePhase\GamePhase;
use App\GameCore\GameElements\GamePhase\GamePhaseException;

class GamePhaseThousandStockDistribution extends GamePhaseThousand implements GamePhase
{
    public const PHASE_KEY = 'stock-distribution';
    protected const PHASE_NAME = 'Cards Sharing';
    protected const PHASE_DESCRIPTION = 'Bidding winner is sharing cards now';

    /**
     * @throws GamePhaseException
     */
    public function getNextPhase(bool $lastAttempt): GamePhase
    {
        if (!$lastAttempt) {
            throw new GamePhaseException(GamePhaseException::MESSAGE_PHASE_SINGLE_ATTEMPT);
        }
        return new GamePhaseThousandDeclaration();
    }
}
