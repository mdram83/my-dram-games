<?php

namespace App\GameCore\GamePlay;

use App\GameCore\GameInvite\GameInvite;

interface GamePlayAbsFactory
{
    public function create(GameInvite $gameInvite): GamePlay;
}
