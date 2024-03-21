<?php

namespace App\GameCore\GamePlayStorage;

use App\GameCore\GameInvite\GameInvite;

interface GamePlayStorage
{
    public function getId(): int|string;
    public function setGameInvite(GameInvite $invite): void;
    public function getGameInvite(): GameInvite;
    public function setGameData(array $data): void;
    public function getGameData(): array;
    public function setSetup(): void;
    public function getSetup(): bool;
}
