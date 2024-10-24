<?php

namespace App\Extensions\Core\GamePlay\Storage;

use App\Models\GamePlayStorageEloquentModel;
use MyDramGames\Core\Exceptions\GamePlayStorageException;
use MyDramGames\Core\GameInvite\GameInvite;
use MyDramGames\Core\GameInvite\GameInviteRepository;
use MyDramGames\Core\GamePlay\Storage\GamePlayStorage;
use MyDramGames\Core\GamePlay\Storage\GamePlayStorageRepository;

readonly class GamePlayStorageRepositoryEloquent implements GamePlayStorageRepository
{
    public function __construct(private GameInviteRepository $inviteRepository)
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
