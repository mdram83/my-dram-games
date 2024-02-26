<?php

namespace App\GameCore\GameInvite;

use App\GameCore\Player\Player;

interface GameInviteFactory
{
    public function create(string $slug, array $options, Player $host): GameInvite;
}
