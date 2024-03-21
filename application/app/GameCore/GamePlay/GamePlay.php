<?php

namespace App\GameCore\GamePlay;

use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\CollectionGamePlayPlayers;

interface GamePlay
{
    public function getId(): int|string;
    public function getPlayers(): CollectionGamePlayPlayers;
    public function handleMove(Player $player, GameMove $move): void;
    public function getSituation(Player $player): GameSituation;
}
