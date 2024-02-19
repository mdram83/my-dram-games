<?php

namespace App\Models\GameCore\Player;

interface PlayerAnonymousFactory
{
    /**
     * @param array<string> $attributes
     */
    public function create(array $attributes = []): PlayerAnonymous;
}
