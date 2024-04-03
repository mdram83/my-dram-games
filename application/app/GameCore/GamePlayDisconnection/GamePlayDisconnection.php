<?php

namespace App\GameCore\GamePlayDisconnection;

use App\GameCore\GamePlay\GamePlay;
use App\GameCore\Player\Player;

interface GamePlayDisconnection
{
    public function refresh(int $durationInSeconds): void;
    public function getPlayer(): Player;
    public function getGamePlay(): GamePlay;
    public function hasExpired(): bool;
    public function remove(): void;
}
