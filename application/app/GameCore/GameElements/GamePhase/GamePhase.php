<?php

namespace App\GameCore\GameElements\GamePhase;

use App\GameCore\GameElements\GameMove\GameMove;

interface GamePhase
{
    public function getKey(): string;
    public function getName(): string;
    public function getDescription(): string;
    public function getNextPhase(bool $lastAttempt): GamePhase;
}
