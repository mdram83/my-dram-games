<?php

namespace App\Extensions\Core\GamePlay\Storage;

use MyDramGames\Core\Exceptions\GamePlayStorageException;
use MyDramGames\Core\GameInvite\GameInvite;
use MyDramGames\Core\GameInvite\GameInviteRepository;
use MyDramGames\Core\GamePlay\Storage\GamePlayStorage;
use MyDramGames\Core\GamePlay\Storage\GamePlayStorageFactory;

readonly class GamePlayStorageFactoryEloquent implements GamePlayStorageFactory
{
    public function __construct(private GameInviteRepository $inviteRepository)
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
