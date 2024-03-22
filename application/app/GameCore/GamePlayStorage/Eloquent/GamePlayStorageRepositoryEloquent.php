<?php

namespace App\GameCore\GamePlayStorage\Eloquent;

use App\GameCore\GameInvite\GameInviteRepository;
use App\GameCore\GamePlayStorage\GamePlayStorage;
use App\GameCore\GamePlayStorage\GamePlayStorageException;

class GamePlayStorageRepositoryEloquent implements \App\GameCore\GamePlayStorage\GamePlayStorageRepository
{

    public function __construct(private readonly GameInviteRepository $inviteRepository)
    {

    }

    /**
     * @throws GamePlayStorageException
     */
    public function getOne(int|string $id): GamePlayStorage
    {
        return new GamePlayStorageEloquent($this->inviteRepository, $id);
    }
}
