<?php

namespace App\Services\GamePlayDisconnection;

use MyDramGames\Core\GamePlay\GamePlay;
use MyDramGames\Utils\Player\Player;

interface GamePlayDisconnectionFactory
{
    public function create(GamePlay $gamePlay, Player $player): GamePlayDisconnection;
}
