<?php

namespace App\GameCore\GameRecord;

use App\GameCore\Player\Player;

interface GameRecord
{
    public function getPlayer(): Player;
    public function getScore(): array;
    public function isWinner(): bool;
}
