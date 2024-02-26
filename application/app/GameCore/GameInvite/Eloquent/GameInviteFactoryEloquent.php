<?php

namespace App\GameCore\GameInvite\Eloquent;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteException;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameBox\GameBoxRepository;
use App\GameCore\Player\Player;

class GameInviteFactoryEloquent implements GameInviteFactory
{
    public function __construct(private readonly GameBoxRepository $gameBoxRepository)
    {

    }

    /**
     * @throws GameInviteException
     */
    public function create(string $slug, array $options, Player $host): GameInvite
    {
        $game = new GameInviteEloquent($this->gameBoxRepository);

        $game->setGameBox($this->gameBoxRepository->getOne($slug));
        $game->setOptions($options);
        $game->addPlayer($host, true);

        return $game;
    }
}
