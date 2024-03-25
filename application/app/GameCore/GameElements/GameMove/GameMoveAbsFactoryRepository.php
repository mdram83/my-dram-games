<?php

namespace App\GameCore\GameElements\GameMove;

interface GameMoveAbsFactoryRepository
{
    public function getOne(string $slug): GameMoveAbsFactory;
}
