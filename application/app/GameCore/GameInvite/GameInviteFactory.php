<?php

namespace App\GameCore\GameInvite;

use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\CollectionGameOptionValueInput;

interface GameInviteFactory
{
    public function create(string $slug, CollectionGameOptionValueInput $options, Player $host): GameInvite;
}
