<?php

namespace App\Games\Thousand;

use App\GameCore\GameOption\GameOptionException;
use App\GameCore\GameSetup\GameSetup;
use App\GameCore\GameSetup\GameSetupAbsFactory;
use App\GameCore\GameSetup\GameSetupAbsFactoryBase;

class GameSetupAbsFactoryThousand extends GameSetupAbsFactoryBase implements GameSetupAbsFactory
{
    /**
     * @throws GameOptionException
     */
    public function create(): GameSetup
    {
        return new GameSetupThousand(clone $this->collectionHandler);
    }
}
