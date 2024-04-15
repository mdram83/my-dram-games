<?php

namespace App\Games\Thousand\Elements;

use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameElements\GamePhase\GamePhase;

// TODO write tests
class GamePhaseThousandDeclaration extends GamePhaseThousand implements GamePhase
{
    protected const PHASE_KEY = 'declaration';
    protected const PHASE_NAME = 'Declaring points to play';
    protected const PHASE_DESCRIPTION = 'Bidding winner to declare points to play now';

    public function getNextPhase(bool $lastAttempt): GamePhase
    {
        return new GamePhaseThousandStockDistribution();
    }

    public function getMoveResults(GameMove $move): array
    {
        // TODO: Implement getMoveResults() method.
    }
}
