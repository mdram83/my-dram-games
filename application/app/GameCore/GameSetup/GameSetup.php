<?php

namespace App\GameCore\GameSetup;

use App\GameCore\GameOption\GameOption;

interface GameSetup
{
    public function getOption(string $key): GameOption;
    public function getAllOptions(): array;
    public function configureOptions(array $options): void;
    public function isConfigured(): bool;
    public function getNumberOfPlayers(): GameOption;
    public function getAutostart(): GameOption;
}
