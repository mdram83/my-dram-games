<?php

namespace App\GameCore\Services\HashGenerator;

interface HashGenerator
{
    public function generateHash(string $sessionId): string;
}
