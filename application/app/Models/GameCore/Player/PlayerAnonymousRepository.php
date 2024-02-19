<?php

namespace App\Models\GameCore\Player;

interface PlayerAnonymousRepository
{
    public function getOne(string $hash): ?PlayerAnonymous;
}
