<?php

namespace App\Models\GameCore\GameDefinition;

use Illuminate\Support\Facades\Config;

class GameDefinitionRepositoryPhpConfig implements GameDefinitionRepository
{
    public function __construct(private readonly GameDefinitionFactory $factory)
    {

    }

    public function getOne(string $slug): GameDefinition
    {
        return $this->factory->create($slug);
    }

    public function getAll(): array
    {
        return array_map(
            fn($slug) => $this->factory->create($slug),
            array_keys(Config::get('games.gameDefinition'))
        );
    }
}
