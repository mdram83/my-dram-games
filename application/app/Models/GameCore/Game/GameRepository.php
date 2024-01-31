<?php

namespace App\Models\GameCore\Game;

interface GameRepository
{
    public function getOne(string|int $gameId): Game;
}
