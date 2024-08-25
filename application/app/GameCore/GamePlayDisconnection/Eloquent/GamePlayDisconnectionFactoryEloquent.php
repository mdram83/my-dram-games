<?php

namespace App\GameCore\GamePlayDisconnection\Eloquent;

use App\GameCore\GamePlay\GamePlay;
use App\GameCore\GamePlayDisconnection\GamePlayDisconnectException;
use App\GameCore\GamePlayDisconnection\GamePlayDisconnection;
use App\GameCore\GamePlayDisconnection\GamePlayDisconnectionFactory;
use App\Models\GamePlayDisconnectionEloquentModel;
use Illuminate\Database\UniqueConstraintViolationException;
use MyDramGames\Utils\Player\Player;

class GamePlayDisconnectionFactoryEloquent implements GamePlayDisconnectionFactory
{
    /**
     * @throws GamePlayDisconnectException
     */
    public function create(GamePlay $gamePlay, Player $player): GamePlayDisconnection
    {
        try {
            $disconnection = new GamePlayDisconnectionEloquentModel();
            $disconnection->setGamePlay($gamePlay);
            $disconnection->setPlayer($player);
            $disconnection->setDisconnectedAt();
            $disconnection->save();

            return $disconnection;

        } catch (UniqueConstraintViolationException) {
            throw new GamePlayDisconnectException(GamePlayDisconnectException::MESSAGE_RECORD_ALREADY_EXIST);
        }

    }
}
