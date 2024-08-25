<?php

namespace App\GameCore\Player;

use MyDramGames\Utils\Player\PlayerAnonymous;

interface PlayerAnonymousRepository
{
    public function getOne(string $hash): ?PlayerAnonymous;
}
