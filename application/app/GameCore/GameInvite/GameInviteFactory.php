<?php

namespace App\GameCore\GameInvite;

use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\Player\Player;

interface GameInviteFactory
{
    public function create(string $slug, CollectionGameOptionValueInput $options, Player $host): GameInvite;
}
