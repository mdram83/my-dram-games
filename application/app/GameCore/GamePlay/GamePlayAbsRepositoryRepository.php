<?php

namespace App\GameCore\GamePlay;

interface GamePlayAbsRepositoryRepository
{
    public function getOne(string $slug): string;
}
