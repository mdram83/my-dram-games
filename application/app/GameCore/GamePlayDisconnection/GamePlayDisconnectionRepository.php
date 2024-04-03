<?php

namespace App\GameCore\GamePlayDisconnection;

use App\GameCore\GamePlay\GamePlay;
use App\GameCore\Player\Player;

interface GamePlayDisconnectionRepository
{
    public function getOneByGamePlayAndPlayer(GamePlay $gamePlay, Player $player): ?GamePlayDisconnection;
}
