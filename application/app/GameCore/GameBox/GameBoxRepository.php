<?php

namespace App\GameCore\GameBox;

interface GameBoxRepository
{
    public function getOne(string $slug): GameBox;
    public function getAll(): array;
}
