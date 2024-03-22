<?php

namespace App\GameCore\GamePlay;

interface GamePlayRepository
{
    public function getOne(int|string $gamePlayId): GamePlay;
}
