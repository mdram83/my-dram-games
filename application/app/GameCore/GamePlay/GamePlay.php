<?php

namespace App\GameCore\GamePlay;

use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameElements\GamePlayPlayers\CollectionGamePlayPlayers;
use App\GameCore\GameElements\GameSituation\GameSituation;
use App\GameCore\Player\Player;

interface GamePlay
{
    public function getId(): int|string;
    public function getPlayers(): CollectionGamePlayPlayers;
    public function handleMove(Player $player, GameMove $move): void;
    public function getSituation(Player $player): array;
}
