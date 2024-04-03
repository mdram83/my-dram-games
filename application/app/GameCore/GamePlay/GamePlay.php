<?php

namespace App\GameCore\GamePlay;

use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameElements\GamePlayPlayers\CollectionGamePlayPlayers;
use App\GameCore\GameInvite\GameInvite;
use App\GameCore\Player\Player;

interface GamePlay
{
    public function getId(): int|string;
    public function getPlayers(): CollectionGamePlayPlayers;
    public function getGameInvite(): GameInvite;
    public function handleMove(GameMove $move): void;
    public function handleForfeit(Player $player): void;
    public function getSituation(Player $player): array;
    public function isFinished(): bool;
}
