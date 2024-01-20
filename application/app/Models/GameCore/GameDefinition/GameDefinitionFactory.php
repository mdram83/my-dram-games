<?php

namespace App\Models\GameCore\GameDefinition;

interface GameDefinitionFactory
{
    public function create(string $slug): GameDefinition;
}
