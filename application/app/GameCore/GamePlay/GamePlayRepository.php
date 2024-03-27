<?php

namespace App\GameCore\GamePlay;

use App\GameCore\GameInvite\GameInvite;

interface GamePlayRepository
{
    public function getOne(int|string $gamePlayId): GamePlay;
    public function getOneByGameInvite(GameInvite $gameInvite): ?GamePlay;
}
