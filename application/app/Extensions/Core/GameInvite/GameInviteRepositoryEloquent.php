<?php

namespace App\Extensions\Core\GameInvite;

use MyDramGames\Core\Exceptions\GameInviteException;
use MyDramGames\Core\GameBox\GameBoxRepository;
use MyDramGames\Core\GameInvite\GameInvite;
use MyDramGames\Core\GameInvite\GameInviteRepository;
use MyDramGames\Core\GameOption\GameOptionConfigurationCollection;
use MyDramGames\Utils\Exceptions\CollectionException;
use MyDramGames\Utils\Player\PlayerCollection;

readonly class GameInviteRepositoryEloquent implements GameInviteRepository
{
    public function __construct(
        private GameBoxRepository $boxRepository,
        private PlayerCollection $playersHandler,
        private GameOptionConfigurationCollection $configurationsHandler,
    )
    {

    }

    /**
     * @throws GameInviteException|CollectionException
     */
    public function getOne(int|string $gameId): GameInvite
    {
        return new GameInviteEloquent(
            $this->boxRepository,
            $this->playersHandler->clone()->reset(),
            $this->configurationsHandler->clone()->reset(),
            $gameId
        );
    }
}
