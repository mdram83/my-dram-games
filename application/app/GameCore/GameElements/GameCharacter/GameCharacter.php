<?php

namespace App\GameCore\GameElements\GameCharacter;

use App\GameCore\Player\Player;

interface GameCharacter
{
    public function getName(): string;
    public function getPlayer(): Player;
}
