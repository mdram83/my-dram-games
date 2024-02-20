<?php

namespace App\GameCore\GameInvite;

interface GameInviteRepository
{
    public function getOne(string|int $gameId): GameInvite;
}
