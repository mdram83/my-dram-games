<?php

namespace App\Models\GameCore\Game;

use App\Models\GameCore\GameDefinition\GameDefinitionFactory;

class GameRepositoryEloquent implements GameRepository
{

    public function __construct(private readonly GameDefinitionFactory $gameDefinitionFactory)
    {

    }

    public function getOne(int|string $gameId): Game
    {
        return new GameEloquent($this->gameDefinitionFactory, $gameId);
    }
}
