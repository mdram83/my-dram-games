<?php

namespace App\Models\GameCore\GameDefinition;

interface GameDefinition
{
    public function getName(): string;
    public function getSlug(): string;
    public function getDescription(): ?string;
    public function getNumberOfPlayers(): array;
    public function getNumberOfPlayersDescription(): string;
    public function getDurationInMinutes(): ?int;
    public function getMinPlayerAge(): ?int;
    public function isActive(): bool;
}
