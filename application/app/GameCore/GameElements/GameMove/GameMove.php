<?php

namespace App\GameCore\GameElements\GameMove;

use App\GameCore\Player\Player;

interface GameMove
{
    public function getPlayer(): Player;
    public function getDetails(): array;
}
