<?php

namespace App\GameCore\GamePlay\Generic;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GamePlay\GamePlay;
use App\GameCore\GamePlay\GamePlayAbsRepositoryRepository;
use App\GameCore\GamePlay\GamePlayRepository;
use App\GameCore\GamePlayStorage\GamePlayStorageRepository;
use App\GameCore\GameRecord\GameRecordFactory;
use App\GameCore\Services\Collection\Collection;

class GamePlayRepositoryGeneric implements GamePlayRepository
{
    public function __construct(
        readonly private GamePlayStorageRepository $storageRepository,
        readonly private GamePlayAbsRepositoryRepository $gamePlayAbsRepository,
        readonly private Collection $collectionHandler,
        readonly private GameRecordFactory $gameRecordFactory,
    )
    {

    }

    public function getOne(int|string $gamePlayId): GamePlay
    {
        $storage = $this->storageRepository->getOne($gamePlayId);
        $slug = $storage->getGameInvite()->getGameBox()->getSlug();
        $className = $this->gamePlayAbsRepository->getOne($slug);

        return new $className($storage, $this->collectionHandler, $this->gameRecordFactory);
    }

    public function getOneByGameInvite(GameInvite $gameInvite): ?GamePlay
    {
        if ($storage = $this->storageRepository->getOneByGameInvite($gameInvite)) {
            return $this->getOne($storage->getId());
        }
        return null;
    }
}
