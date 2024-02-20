<?php

namespace App\GameCore\GameInvite;

use App\GameCore\Player\Player;

interface GameInviteFactory
{
    public function create(string $slug, int $numberOfPlayers, Player $host): GameInvite;
}
