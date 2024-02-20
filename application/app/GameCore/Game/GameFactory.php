<?php

namespace App\GameCore\Game;

use App\GameCore\Player\Player;

interface GameFactory
{
    public function create(string $slug, int $numberOfPlayers, Player $host): Game;
}
