<?php

namespace App\GameCore\GamePlay;

interface GamePlayAbsFactoryRepository
{
    public function getOne(string $slug): GamePlayAbsFactory;
}
