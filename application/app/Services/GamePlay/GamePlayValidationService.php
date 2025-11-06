<?php

namespace App\Services\GamePlay;

use MyDramGames\Core\GamePlay\GamePlay;
use MyDramGames\Utils\Player\Player;

interface GamePlayValidationService
{
    public function validateGamePlayPlayer(GamePlay $gamePlay, Player $player): void;
}
