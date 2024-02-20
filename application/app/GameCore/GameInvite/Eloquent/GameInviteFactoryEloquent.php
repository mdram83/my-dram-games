<?php

namespace App\GameCore\GameInvite\Eloquent;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameBox\GameBoxRepository;
use App\GameCore\Player\Player;

class GameInviteFactoryEloquent implements GameInviteFactory
{
    public function __construct(private readonly GameBoxRepository $gameDefinitionRepository)
    {

    }

    public function create(string $slug, int $numberOfPlayers, Player $host): GameInvite
    {
        $game = new GameInviteEloquent($this->gameDefinitionRepository);

        $game->setGameDefinition($this->gameDefinitionRepository->getOne($slug));
        $game->setNumberOfPlayers($numberOfPlayers);
        $game->addPlayer($host, true);

        return $game;
    }
}
