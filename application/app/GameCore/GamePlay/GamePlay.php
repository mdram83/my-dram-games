<?php

namespace App\GameCore\GamePlay;

use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameInvite\GameInvite;
use MyDramGames\Utils\Player\Player;
use MyDramGames\Utils\Player\PlayerCollection;

interface GamePlay
{
    public function getId(): int|string;
    public function getPlayers(): PlayerCollection;
    public function getActivePlayer(): ?Player;
    public function getGameInvite(): GameInvite;
    public function handleMove(GameMove $move): void;
    public function handleForfeit(Player $player): void;
    public function getSituation(Player $player): array;
    public function isFinished(): bool;
}
