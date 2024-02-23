<?php

namespace App\Games\TicTacToe;

use App\GameCore\GameSetup\GameSetup;
use App\GameCore\GameSetup\GameSetupAbsFactory;
use App\GameCore\GameSetup\GameSetupException;

class GameSetupAbsFactoryTicTacToe implements GameSetupAbsFactory
{
    /**
     * @throws GameSetupException
     */
    public function create(array $options = []): GameSetup
    {
        return new GameSetupTicTacToe($options);
    }
}
