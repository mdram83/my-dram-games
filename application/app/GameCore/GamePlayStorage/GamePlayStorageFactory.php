<?php

namespace App\GameCore\GamePlayStorage;

use App\GameCore\GameInvite\GameInvite;

interface GamePlayStorageFactory
{
    public function create(GameInvite $gameInvite): GamePlayStorage;
}
