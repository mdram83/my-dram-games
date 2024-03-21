<?php

namespace App\GameCore\GamePlay;

use App\GameCore\Player\Player;

interface GameCharacter
{
    public function getName(): string;
    public function getPlayer(): Player;
}
