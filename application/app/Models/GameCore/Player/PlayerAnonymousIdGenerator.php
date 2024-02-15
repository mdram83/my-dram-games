<?php

namespace App\Models\GameCore\Player;

interface PlayerAnonymousIdGenerator
{
    public function generateId(string $sessionId): string;
}
