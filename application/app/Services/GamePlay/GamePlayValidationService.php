<?php

namespace App\Services\GamePlay;

use MyDramGames\Core\GamePlay\GamePlay;
use MyDramGames\Utils\Player\Player;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

interface GamePlayValidationService
{
    /**
     * @throws AccessDeniedHttpException
     */
    public function validateGamePlayPlayer(GamePlay $gamePlay, Player $player): void;

    /**
     * @throws GamePlayValidationServiceException
     */
    public function validateGamePlayNotFinished(GamePlay $gamePlay): void;

    /**
     * @throws GamePlayValidationServiceException|AccessDeniedHttpException
     */
    public function validateDisconnectionApplicable(GamePlay $gamePlay, Player $player): void;
}
