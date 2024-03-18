<?php

namespace App\GameCore\GamePlay;

use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\CollectionGamePlayPlayers;

interface GamePlay
{
    public function getPlayers(): CollectionGamePlayPlayers;
    public function handleMove(GameMove $move): void;
    public function getStatus(Player $player): GameStatus;
}
