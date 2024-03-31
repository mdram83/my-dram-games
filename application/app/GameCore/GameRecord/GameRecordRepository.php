<?php

namespace App\GameCore\GameRecord;

use App\GameCore\GameInvite\GameInvite;

interface GameRecordRepository
{
    public function getByGameInvite(GameInvite $gameInvite): CollectionGameRecord;
}
