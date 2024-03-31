<?php

namespace App\GameCore\GameResult;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameRecord\CollectionGameRecord;

interface GameResultProvider
{
    public function getResult(mixed $data): ?GameResult;
    public function createGameRecords(GameInvite $gameInvite): CollectionGameRecord;
}
