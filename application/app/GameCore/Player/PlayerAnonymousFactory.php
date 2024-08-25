<?php

namespace App\GameCore\Player;

use MyDramGames\Utils\Player\PlayerAnonymous;

interface PlayerAnonymousFactory
{
    /**
     * @param array<string> $attributes
     */
    public function create(array $attributes = []): PlayerAnonymous;
}
