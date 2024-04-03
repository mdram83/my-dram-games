<?php

namespace App\GameCore\GamePlayDisconnection;

use App\GameCore\GamePlay\GamePlay;
use App\GameCore\Player\Player;
use DateTimeImmutable;

interface GamePlayDisconnection
{
    public function setGamePlay(GamePlay $gamePlay): void;
    public function setPlayer(Player $player): void;
    public function setDisconnectedAt(): void;
    public function hasExpired(DateTimeImmutable $expiredAt): bool;
    public function remove(): void;
}
