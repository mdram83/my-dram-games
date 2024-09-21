<?php

namespace App\Services\GamePlayDisconnection;

use MyDramGames\Core\GamePlay\GamePlay;
use MyDramGames\Utils\Player\Player;

interface GamePlayDisconnectionRepository
{
    public function getOneByGamePlayAndPlayer(GamePlay $gamePlay, Player $player): ?GamePlayDisconnection;
}
