<?php

namespace App\Models\GameCore\GameDefinition;

class GameDefinitionFactoryPhpConfig implements GameDefinitionFactory
{
    public function create(string $slug): GameDefinition
    {
        return new GameDefinitionPhpConfig($slug);
    }
}
