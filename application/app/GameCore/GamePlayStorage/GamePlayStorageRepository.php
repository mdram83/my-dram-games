<?php

namespace App\GameCore\GamePlayStorage;

use App\GameCore\GameInvite\GameInvite;

interface GamePlayStorageRepository
{
    public function getOne(int|string $id): GamePlayStorage;
    public function getOneByGameInvite(GameInvite $gameInvite): ?GamePlayStorage;
}
