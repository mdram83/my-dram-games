<?php

namespace App\GameCore\GameResult;

interface GameResultProvider
{
    public function getResult(mixed $data): ?GameResult;
//    public function createGameRecords(): void;
}
