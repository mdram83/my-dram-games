<?php

namespace App\Games\Thousand\Elements;

use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameElements\GamePhase\GamePhase;
use App\GameCore\GameElements\GamePhase\GamePhaseException;

class GamePhaseThousandDeclaration extends GamePhaseThousand implements GamePhase
{
    public const PHASE_KEY = 'declaration';
    protected const PHASE_NAME = 'Declaring points to play';
    protected const PHASE_DESCRIPTION = 'Bidding winner to declare points to play now';

    /**
     * @throws GamePhaseException
     */
    public function getNextPhase(bool $lastAttempt): GamePhase
    {
        if (!$lastAttempt) {
            throw new GamePhaseException(GamePhaseException::MESSAGE_PHASE_SINGLE_ATTEMPT);
        }
        return new GamePhaseThousandPlayFirstCard();
    }
}
