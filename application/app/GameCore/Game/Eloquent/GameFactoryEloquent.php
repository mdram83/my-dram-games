<?php

namespace App\GameCore\Game\Eloquent;

use App\GameCore\Game\Game;
use App\GameCore\Game\GameFactory;
use App\GameCore\GameDefinition\GameDefinitionRepository;
use App\GameCore\Player\Player;

class GameFactoryEloquent implements GameFactory
{
    public function __construct(private readonly GameDefinitionRepository $gameDefinitionRepository)
    {

    }

    public function create(string $slug, int $numberOfPlayers, Player $host): Game
    {
        $game = new GameEloquent($this->gameDefinitionRepository);

        $game->setGameDefinition($this->gameDefinitionRepository->getOne($slug));
        $game->setNumberOfPlayers($numberOfPlayers);
        $game->addPlayer($host, true);

        return $game;
    }
}
