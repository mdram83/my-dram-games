<?php

namespace App\Extensions\Core\GamePlay;

use MyDramGames\Core\Exceptions\GameBoxException;
use MyDramGames\Core\Exceptions\GamePlayStorageException;
use MyDramGames\Core\GameInvite\GameInvite;
use MyDramGames\Core\GamePlay\GamePlay;
use MyDramGames\Core\GamePlay\GamePlayRepository;
use MyDramGames\Core\GamePlay\Services\GamePlayServicesProvider;
use MyDramGames\Core\GamePlay\Storage\GamePlayStorageRepository;

readonly class GamePlayStorableRepository implements GamePlayRepository
{
    public function __construct(
        private GamePlayStorageRepository $storageRepository,
        private GamePlayServicesProvider $gamePlayServicesProvider,
    )
    {

    }

    /**
     * @throws GamePlayStorageException
     * @throws GameBoxException
     */
    public function getOne(int|string $gamePlayId): GamePlay
    {
        $storage = $this->storageRepository->getOne($gamePlayId);
        $classname = $storage->getGameInvite()->getGameBox()->getGamePlayClassname();

        return new $classname($storage, $this->gamePlayServicesProvider);
    }

    /**
     * @throws GamePlayStorageException
     * @throws GameBoxException
     */
    public function getOneByGameInvite(GameInvite $gameInvite): ?GamePlay
    {
        if ($storage = $this->storageRepository->getOneByGameInvite($gameInvite)) {
            return $this->getOne($storage->getId());
        }
        return null;
    }
}
