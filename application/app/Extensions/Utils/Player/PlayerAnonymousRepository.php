<?php

namespace App\Extensions\Utils\Player;

use MyDramGames\Utils\Player\PlayerAnonymous;

interface PlayerAnonymousRepository
{
    public function getOne(string $hash): ?PlayerAnonymous;
}
