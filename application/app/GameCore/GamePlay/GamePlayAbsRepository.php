<?php

namespace App\GameCore\GamePlay;

interface GamePlayAbsRepository
{
    public function getOne(string $slug): string;
}
