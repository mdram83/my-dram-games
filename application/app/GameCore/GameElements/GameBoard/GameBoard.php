<?php

namespace App\GameCore\GameElements\GameBoard;

interface GameBoard
{
    public function toJson(): string;
    public function setFromJson(string $jsonBoard): void;
}
