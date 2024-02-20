<?php

namespace App\GameCore\Services\HashGenerator;

interface PlayerAnonymousHashGenerator
{
    public function generateHash(string $sessionId): string;
}
