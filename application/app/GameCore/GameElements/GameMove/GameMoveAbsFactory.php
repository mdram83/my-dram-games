<?php

namespace App\GameCore\GameElements\GameMove;

use MyDramGames\Utils\Player\Player;

interface GameMoveAbsFactory
{
    public function create(Player $player, array $inputs): GameMove;
}
