<?php

namespace App\GameCore\GameSetup;

interface GameSetup
{
    public function getOption(string $name): array;
    public function getAllOptions(): array;
    public function getNumberOfPlayers(): array;
    public function getAutostart(): array;
}
