<?php

namespace App\Games\TicTacToe;

use App\GameCore\GameSetup\GameSetup;
use App\GameCore\GameSetup\GameSetupAbsFactory;

class GameSetupAbsFactoryTicTacToe implements GameSetupAbsFactory
{
    public function create(): GameSetup
    {
        return new GameSetupTicTacToe();
    }
}
