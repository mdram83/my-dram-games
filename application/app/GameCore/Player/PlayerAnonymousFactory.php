<?php

namespace App\GameCore\Player;

interface PlayerAnonymousFactory
{
    /**
     * @param array<string> $attributes
     */
    public function create(array $attributes = []): PlayerAnonymous;
}
