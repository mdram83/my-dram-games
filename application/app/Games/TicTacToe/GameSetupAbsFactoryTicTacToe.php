<?php

namespace App\Games\TicTacToe;

use App\GameCore\GameOption\GameOptionException;
use App\GameCore\GameSetup\GameSetup;
use App\GameCore\GameSetup\GameSetupAbsFactory;
use App\GameCore\GameSetup\GameSetupAbsFactoryBase;
use App\GameCore\Services\Collection\Collection;

class GameSetupAbsFactoryTicTacToe extends GameSetupAbsFactoryBase implements GameSetupAbsFactory
{
    /**
     * @throws GameOptionException
     */
    public function create(): GameSetup
    {
        return new GameSetupTicTacToe($this->collectionHandler);
    }
}
