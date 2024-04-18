<?php

namespace App\Games\Thousand\Elements;

use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameElements\GamePhase\GamePhase;

// TODO write tests
class GamePhaseThousandStockDistribution extends GamePhaseThousand implements GamePhase
{
    public const PHASE_KEY = 'stock-distribution';
    protected const PHASE_NAME = 'Cards Sharing';
    protected const PHASE_DESCRIPTION = 'Bidding winner is sharing cards now';

    public function getNextPhase(bool $lastAttempt): GamePhase
    {
        return new GamePhaseThousandDeclaration();
    }

    public function getMoveResults(GameMove $move): array
    {
        // TODO: Implement getMoveResults() method.
    }
}
