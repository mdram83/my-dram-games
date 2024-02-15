<?php

namespace App\Models\GameCore\Player;

interface PlayerAnonymousHashGenerator
{
    public function generateHash(string $sessionId): string;
}
