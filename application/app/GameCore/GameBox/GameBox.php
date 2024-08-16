<?php

namespace App\GameCore\GameBox;

use App\GameCore\GameSetup\GameSetup;

interface GameBox
{
    public function getName(): string;
    public function getSlug(): string;
    public function getDescription(): ?string;
    public function getNumberOfPlayersDescription(): string;
    public function getDurationInMinutes(): ?int;
    public function getMinPlayerAge(): ?int;
    public function isActive(): bool;
    public function isPremium(): bool;
    public function getGameSetup(): GameSetup;
    public function toArray(): array;
}
