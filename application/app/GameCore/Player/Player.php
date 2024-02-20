<?php

namespace App\GameCore\Player;

interface Player
{
    public function getId(): int|string;
    public function getName(): string;
    public function isRegistered(): bool;
}
