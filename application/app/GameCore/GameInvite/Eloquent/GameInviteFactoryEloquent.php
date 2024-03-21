<?php

namespace App\GameCore\GameInvite\Eloquent;

use App\GameCore\GameBox\GameBoxRepository;
use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteException;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\Collection;

class GameInviteFactoryEloquent implements GameInviteFactory
{
    public function __construct(
        private readonly GameBoxRepository $gameBoxRepository,
        private readonly Collection $optionsHandler,
    )
    {

    }

    /**
     * @throws GameInviteException
     */
    public function create(string $slug, CollectionGameOptionValueInput $options, Player $host): GameInvite
    {
        $game = new GameInviteEloquent($this->gameBoxRepository, $this->optionsHandler);

        $game->setGameBox($this->gameBoxRepository->getOne($slug));
        $game->setOptions($options);
        $game->addPlayer($host, true);

        return $game;
    }
}
