<?php

namespace App\Extensions\Core\GameInvite;

use MyDramGames\Core\Exceptions\GameBoxException;
use MyDramGames\Core\Exceptions\GameInviteException;
use MyDramGames\Core\Exceptions\GameOptionException;
use MyDramGames\Core\Exceptions\GameSetupException;
use MyDramGames\Core\GameBox\GameBoxRepository;
use MyDramGames\Core\GameInvite\GameInvite;
use MyDramGames\Core\GameInvite\GameInviteFactory;
use MyDramGames\Core\GameOption\GameOptionConfigurationCollection;
use MyDramGames\Utils\Exceptions\CollectionException;
use MyDramGames\Utils\Player\Player;
use MyDramGames\Utils\Player\PlayerCollection;

readonly class GameInviteFactoryEloquent implements GameInviteFactory
{
    public function __construct(
        private GameBoxRepository $gameBoxRepository,
        private PlayerCollection $playersHandler,
        private GameOptionConfigurationCollection $optionsHandler,
    )
    {

    }

    /**
     * @throws CollectionException
     * @throws GameInviteException
     * @throws GameSetupException
     * @throws GameOptionException
     * @throws GameBoxException
     */
    public function create(string $slug, GameOptionConfigurationCollection $configurations, Player $host): GameInvite
    {
        $game = new GameInviteEloquent(
            $this->gameBoxRepository,
            $this->playersHandler->clone()->reset(),
            $this->optionsHandler->clone()->reset(),
        );

        $game->setGameBox($this->gameBoxRepository->getOne($slug));
        $game->setOptions($configurations);
        $game->addPlayer($host, true);

        return $game;
    }
}
