<?php

namespace App\GameCore\GameResult;

use App\GameCore\GameElements\GamePlayPlayers\CollectionGamePlayPlayers;

interface GameResult
{
    public function getMessage(): string;
    public function getDetails(): array;
    public function toArray(): array;
}
