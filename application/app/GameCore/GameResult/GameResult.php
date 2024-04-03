<?php

namespace App\GameCore\GameResult;

interface GameResult
{
    public function getMessage(): string;
    public function getDetails(): array;
    public function toArray(): array;
}
