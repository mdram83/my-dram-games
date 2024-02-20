<?php

namespace App\GameCore\Game\Eloquent;

use App\GameCore\Game\Game;
use App\GameCore\Game\GameRepository;
use App\GameCore\GameDefinition\GameDefinitionRepository;

class GameRepositoryEloquent implements GameRepository
{

    public function __construct(private readonly GameDefinitionRepository $gameDefinitionRepository)
    {

    }

    public function getOne(int|string $gameId): Game
    {
        return new GameEloquent($this->gameDefinitionRepository, $gameId);
    }
}
