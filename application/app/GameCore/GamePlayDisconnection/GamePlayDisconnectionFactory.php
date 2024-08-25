<?php

namespace App\GameCore\GamePlayDisconnection;

use App\GameCore\GamePlay\GamePlay;
use MyDramGames\Utils\Player\Player;

interface GamePlayDisconnectionFactory
{
    public function create(GamePlay $gamePlay, Player $player): GamePlayDisconnection;
}
