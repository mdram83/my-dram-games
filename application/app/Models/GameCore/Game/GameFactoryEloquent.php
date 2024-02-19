<?php

namespace App\Models\GameCore\Game;

use App\Models\GameCore\GameDefinition\GameDefinitionRepository;
use App\Models\GameCore\Player\Player;

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
