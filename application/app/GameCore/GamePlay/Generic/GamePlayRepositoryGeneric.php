<?php

namespace App\GameCore\GamePlay\Generic;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GamePlay\GamePlay;
use App\GameCore\GamePlay\GamePlayAbsRepository;
use App\GameCore\GamePlay\GamePlayRepository;
use App\GameCore\GamePlay\GamePlayServicesProvider;
use App\GameCore\GamePlayStorage\GamePlayStorageRepository;
use App\GameCore\GameRecord\GameRecordFactory;
use App\GameCore\Services\Collection\Collection;

class GamePlayRepositoryGeneric implements GamePlayRepository
{
    public function __construct(
        readonly private GamePlayStorageRepository $storageRepository,
        readonly private GamePlayAbsRepository $gamePlayAbsRepository,
        readonly private GamePlayServicesProvider $gamePlayServicesProvider,
    )
    {

    }

    public function getOne(int|string $gamePlayId): GamePlay
    {
        $storage = $this->storageRepository->getOne($gamePlayId);
        $slug = $storage->getGameInvite()->getGameBox()->getSlug();
        $className = $this->gamePlayAbsRepository->getOne($slug);

        return new $className($storage, $this->gamePlayServicesProvider);
    }

    public function getOneByGameInvite(GameInvite $gameInvite): ?GamePlay
    {
        if ($storage = $this->storageRepository->getOneByGameInvite($gameInvite)) {
            return $this->getOne($storage->getId());
        }
        return null;
    }
}
