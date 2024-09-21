<?php

namespace App\Services\HashGenerator;

interface HashGenerator
{
    public function generateHash(string $key): string;
}
