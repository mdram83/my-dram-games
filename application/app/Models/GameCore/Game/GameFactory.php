<?php

namespace App\Models\GameCore\Game;

use App\Models\GameCore\Player\Player;

interface GameFactory
{
    public function create(string $slug, int $numberOfPlayers, Player $host): Game;
}
