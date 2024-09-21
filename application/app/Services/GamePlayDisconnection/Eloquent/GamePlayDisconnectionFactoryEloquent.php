<?php

namespace App\Services\GamePlayDisconnection\Eloquent;

use App\Services\GamePlayDisconnection\GamePlayDisconnectException;
use App\Services\GamePlayDisconnection\GamePlayDisconnection;
use App\Services\GamePlayDisconnection\GamePlayDisconnectionFactory;
use App\Models\GamePlayDisconnectionEloquentModel;
use Illuminate\Database\UniqueConstraintViolationException;
use MyDramGames\Core\GamePlay\GamePlay;
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
