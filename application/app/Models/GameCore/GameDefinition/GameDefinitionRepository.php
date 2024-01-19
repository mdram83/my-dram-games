<?php

namespace App\Models\GameCore\GameDefinition;

interface GameDefinitionRepository
{
    public function getOne(string $slug): GameDefinition;
    public function getAll(): array;
}
