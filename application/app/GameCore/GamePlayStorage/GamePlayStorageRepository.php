<?php

namespace App\GameCore\GamePlayStorage;

interface GamePlayStorageRepository
{
    public function getOne(int|string $id): GamePlayStorage;
}
