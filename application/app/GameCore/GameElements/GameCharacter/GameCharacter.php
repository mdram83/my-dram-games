<?php

namespace App\GameCore\GameElements\GameCharacter;

use MyDramGames\Utils\Player\Player;

interface GameCharacter
{
    public function getName(): string;
    public function getPlayer(): Player;
}
