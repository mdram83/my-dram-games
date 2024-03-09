<?php

namespace App\GameCore\GameOption;

interface GameOptionClassRepository
{
    public function getOne(string $key): string;
}
