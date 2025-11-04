<?php

namespace App\Http\Controllers\Traits;

use MyDramGames\Core\GamePlay\GamePlay;
use MyDramGames\Utils\Player\Player;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

trait ValidateGamePlayPlayerTrait
{
    private function validateGamePlayPlayer(GamePlay $gamePlay, Player $player): void
    {
        if (!$gamePlay->getPlayers()->exist($player->getId())) {
            throw new AccessDeniedHttpException();
        }
    }
}
