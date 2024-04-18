<?php

namespace App\Games\Thousand;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GamePlay\GamePlay;
use App\GameCore\GamePlay\GamePlayAbsFactory;
use App\GameCore\GamePlay\GamePlayAbsFactoryBase;
use App\GameCore\GamePlay\GamePlayException;
use App\GameCore\GamePlay\GamePlayServicesProvider;
use App\GameCore\GamePlayStorage\GamePlayStorageException;
use App\GameCore\GamePlayStorage\GamePlayStorageFactory;
use App\GameCore\GameRecord\GameRecordFactory;
use App\GameCore\Services\Collection\Collection;

class GamePlayAbsFactoryThousand extends GamePlayAbsFactoryBase implements GamePlayAbsFactory
{
    /**
     * @throws GamePlayException
     * @throws GamePlayStorageException
     */
    public function create(GameInvite $gameInvite): GamePlay
    {
        $storage = $this->storageFactory->create($gameInvite);
        return new GamePlayThousand($storage, $this->gamePlayServicesProvider);
    }
}
