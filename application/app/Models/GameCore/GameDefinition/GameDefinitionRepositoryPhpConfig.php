<?php

namespace App\Models\GameCore\GameDefinition;

use Illuminate\Support\Facades\Config;

class GameDefinitionRepositoryPhpConfig implements GameDefinitionRepository
{

    public function getOne(string $slug): GameDefinition
    {
        return new GameDefinitionPhpConfig($slug);
    }

    public function getAll(): array
    {
        return array_map(
            fn($slug) => new GameDefinitionPhpConfig($slug),
            array_keys(Config::get('games.gameDefinition'))
        );
    }
}
