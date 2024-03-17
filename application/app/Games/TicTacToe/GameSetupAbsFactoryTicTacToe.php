<?php

namespace App\Games\TicTacToe;

use App\GameCore\GameOption\GameOptionException;
use App\GameCore\GameSetup\GameSetup;
use App\GameCore\GameSetup\GameSetupAbsFactory;
use App\GameCore\Services\Collection\Collection;

class GameSetupAbsFactoryTicTacToe implements GameSetupAbsFactory
{
    public function __construct(protected Collection $collectionHandler)
    {

    }

    /**
     * @throws GameOptionException
     */
    public function create(): GameSetup
    {
        return new GameSetupTicTacToe($this->collectionHandler);
    }
}
