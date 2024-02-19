<?php

namespace App\Models\GameCore\Game;

use App\Models\GameCore\GameDefinition\GameDefinitionRepository;

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
