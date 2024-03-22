<?php

namespace App\GameCore\GamePlayStorage\Eloquent;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteRepository;
use App\GameCore\GamePlayStorage\GamePlayStorage;
use App\GameCore\GamePlayStorage\GamePlayStorageException;
use App\GameCore\GamePlayStorage\GamePlayStorageFactory;

class GamePlayStorageFactoryEloquent implements GamePlayStorageFactory
{
    public function __construct(readonly private GameInviteRepository $inviteRepository)
    {

    }

    /**
     * @throws GamePlayStorageException
     */
    public function create(GameInvite $gameInvite): GamePlayStorage
    {
        $storage = new GamePlayStorageEloquent($this->inviteRepository);
        $storage->setGameInvite($gameInvite);

        return $storage;
    }
}
