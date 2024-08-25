<?php

namespace App\GameCore\GamePlayDisconnection;

use App\GameCore\GamePlay\GamePlay;
use MyDramGames\Utils\Player\Player;

interface GamePlayDisconnectionRepository
{
    public function getOneByGamePlayAndPlayer(GamePlay $gamePlay, Player $player): ?GamePlayDisconnection;
}
