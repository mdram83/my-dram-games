<?php

namespace App\Models\GameCore\Player;

interface Player
{
    public function getId(): int|string;
    public function getName(): string;
}
