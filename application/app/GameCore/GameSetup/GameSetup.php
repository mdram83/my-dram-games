<?php

namespace App\GameCore\GameSetup;

interface GameSetup
{
    public function getOption(string $name): array;
    public function getAllOptions(): array;
    public function setOptions(array $options): void;
    public function isConfigured(): bool;
    public function getNumberOfPlayers(): array;
    public function getAutostart(): array;
}
