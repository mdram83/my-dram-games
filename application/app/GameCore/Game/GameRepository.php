<?php

namespace App\GameCore\Game;

interface GameRepository
{
    public function getOne(string|int $gameId): Game;
}
