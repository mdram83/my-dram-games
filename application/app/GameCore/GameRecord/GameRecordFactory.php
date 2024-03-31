<?php

namespace App\GameCore\GameRecord;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\Player\Player;

interface GameRecordFactory
{
    public function create(GameInvite $invite, Player $player, bool $isWinner, array $score): GameRecord;
}
