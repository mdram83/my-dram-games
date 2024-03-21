<?php

namespace App\GameCore\GameElements\GameSituation;

use App\GameCore\GameElements\GameBoard\GameBoard;
use App\GameCore\GameElements\GamePlayPlayers\CollectionGamePlayPlayers;
use App\GameCore\Player\Player;

interface GameSituation
{
    public function getPlayers(): CollectionGamePlayPlayers;
    public function getActivePlayer(): Player;
    public function getBoard(): GameBoard;
}
