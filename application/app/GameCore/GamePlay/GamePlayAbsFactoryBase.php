<?php

namespace App\GameCore\GamePlay;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GamePlay\GamePlayAbsFactory;
use App\GameCore\GamePlayStorage\GamePlayStorageFactory;

abstract class GamePlayAbsFactoryBase implements GamePlayAbsFactory
{
    final public function __construct(
        readonly protected GamePlayStorageFactory $storageFactory,
        readonly protected GamePlayServicesProvider $gamePlayServicesProvider,
    )
    {

    }
}
