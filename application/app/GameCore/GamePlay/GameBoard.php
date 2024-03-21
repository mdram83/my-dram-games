<?php

namespace App\GameCore\GamePlay;

interface GameBoard
{
    public function toJson(): string;
    public function setFromJson(string $jsonBoard): void;
}
