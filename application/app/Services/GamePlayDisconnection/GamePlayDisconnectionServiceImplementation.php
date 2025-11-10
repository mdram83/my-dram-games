<?php

namespace App\Services\GamePlayDisconnection;

use App\Http\Controllers\ControllerValidationException;
use MyDramGames\Core\GameOption\Values\GameOptionValueForfeitAfterGeneric;
use MyDramGames\Core\GamePlay\GamePlay;
use MyDramGames\Utils\Player\Player;

class GamePlayDisconnectionServiceImplementation implements GamePlayDisconnectionService
{
    public function getValidatedDisconnectedPlayer(string $disconnectedPlayerName, GamePlay $gamePlay): Player
    {
        $singlePlayerCollection = $gamePlay
            ->getPlayers()
            ->filter(fn($item) => $item->getName() === $disconnectedPlayerName);

        if ($singlePlayerCollection->count() === 0) {
            throw new ControllerValidationException(ControllerValidationException::MESSAGE_INCORRECT_INPUTS);
        }

        return $singlePlayerCollection->pullFirst();
    }

    public function setDisconnection(
        GamePlayDisconnectionRepository $repository,
        GamePlayDisconnectionFactory $factory,
        Player $disconnectedPlayer,
        GamePlay $gamePlay
    ): void {

        $disconnection = $repository->getOneByGamePlayAndPlayer($gamePlay, $disconnectedPlayer);

        if ($disconnection === null) {
            $factory->create($gamePlay, $disconnectedPlayer);
        } else {
            $disconnection->setDisconnectedAt();
            $disconnection->save();
        }
    }

    public function validateForfeitAfterApplicable(
        GamePlayDisconnectionRepository $repository,
        GamePlay $gamePlay,
        Player $disconnectedPlayer
    ):void
    {
        $forfeitAfterOptionValue = $gamePlay
            ->getGameInvite()
            ->getGameSetup()
            ->getOption('forfeitAfter')
            ->getConfiguredValue();

        if ($forfeitAfterOptionValue === GameOptionValueForfeitAfterGeneric::Disabled) {
            throw new ControllerValidationException(ControllerValidationException::MESSAGE_FORFEIT_AFTER_DISABLED);
        }

        $disconnection = $repository->getOneByGamePlayAndPlayer($gamePlay, $disconnectedPlayer);

        if ($disconnection === null) {
            throw new ControllerValidationException(ControllerValidationException::MESSAGE_FORFEIT_AFTER_EARLY);
        }

        if (!$disconnection->hasExpired($forfeitAfterOptionValue->getValue())) {
            throw new ControllerValidationException(ControllerValidationException::MESSAGE_FORFEIT_AFTER_EARLY);
        }
    }
}
