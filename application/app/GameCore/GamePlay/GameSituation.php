<?php

namespace App\GameCore\GamePlay;

use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\CollectionGamePlayPlayers;

interface GameSituation
{
    public function getPlayers(): CollectionGamePlayPlayers;
    public function getActivePlayer(): Player;
    public function getBoard(): GameBoard;
}
