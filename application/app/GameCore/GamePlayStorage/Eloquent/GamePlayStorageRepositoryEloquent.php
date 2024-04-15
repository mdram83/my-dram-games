<?php

namespace App\GameCore\GamePlayStorage\Eloquent;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteRepository;
use App\GameCore\GamePlayStorage\GamePlayStorage;
use App\GameCore\GamePlayStorage\GamePlayStorageException;
use App\GameCore\GamePlayStorage\GamePlayStorageRepository;
use App\Models\GamePlayStorageEloquentModel;

class GamePlayStorageRepositoryEloquent implements GamePlayStorageRepository
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

    /**
     * @throws GamePlayStorageException
     */
    public function getOneByGameInvite(GameInvite $gameInvite): ?GamePlayStorage
    {
        if ($storage = GamePlayStorageEloquentModel::where('gameInviteId', '=' , $gameInvite->getId())->first()) {
            return $this->getOne($storage->id);
        }
        return null;
    }
}
