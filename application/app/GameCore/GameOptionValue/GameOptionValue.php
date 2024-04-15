<?php

namespace App\GameCore\GameOptionValue;

interface GameOptionValue
{
    public function getValue(): int|string|null;
    public function getLabel(): string;
}
