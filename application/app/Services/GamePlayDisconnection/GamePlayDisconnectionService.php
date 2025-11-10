<?php

namespace App\Services\GamePlayDisconnection;

use App\Http\Controllers\ControllerValidationException;
use MyDramGames\Core\Exceptions\GameOptionException;
use MyDramGames\Core\GamePlay\GamePlay;
use MyDramGames\Utils\Exceptions\CollectionException;
use MyDramGames\Utils\Player\Player;

interface GamePlayDisconnectionService
{
    /**
     * @throws ControllerValidationException|CollectionException
     */
    public function getValidatedDisconnectedPlayer(string $disconnectedPlayerName, GamePlay $gamePlay): Player;

    public function setDisconnection(
        GamePlayDisconnectionRepository $repository,
        GamePlayDisconnectionFactory $factory,
        Player $disconnectedPlayer,
        GamePlay $gamePlay
    ): void;

    /**
     * @throws GameOptionException
     * @throws ControllerValidationException
     */
    public function validateForfeitAfterApplicable(
        GamePlayDisconnectionRepository $repository,
        GamePlay $gamePlay,
        Player $disconnectedPlayer
    ): void;

}
