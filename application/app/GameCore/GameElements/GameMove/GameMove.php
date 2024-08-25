<?php

namespace App\GameCore\GameElements\GameMove;

use MyDramGames\Utils\Player\Player;

interface GameMove
{
    public function getPlayer(): Player;
    public function getDetails(): array;
}
