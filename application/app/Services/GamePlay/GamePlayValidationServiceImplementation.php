<?php

namespace App\Services\GamePlay;

use MyDramGames\Core\GamePlay\GamePlay;
use MyDramGames\Utils\Player\Player;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class GamePlayValidationServiceImplementation implements GamePlayValidationService
{

    public function validateGamePlayPlayer(GamePlay $gamePlay, Player $player): void
    {
        if (!$gamePlay->getPlayers()->exist($player->getId())) {
            throw new AccessDeniedHttpException();
        }
    }
}
