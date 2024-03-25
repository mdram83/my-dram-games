<?php

namespace App\GameCore\GameElements\GameMove;

use App\GameCore\Player\Player;

interface GameMoveAbsFactory
{
    public function create(Player $player, array $inputs): GameMove;
}
