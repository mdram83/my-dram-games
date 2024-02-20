<?php

namespace App\GameCore\Player;

interface PlayerAnonymousRepository
{
    public function getOne(string $hash): ?PlayerAnonymous;
}
