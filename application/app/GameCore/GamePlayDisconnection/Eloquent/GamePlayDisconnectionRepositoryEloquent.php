<?php

namespace App\GameCore\GamePlayDisconnection\Eloquent;

use App\GameCore\GamePlay\GamePlay;
use App\GameCore\GamePlayDisconnection\GamePlayDisconnection;
use App\GameCore\GamePlayDisconnection\GamePlayDisconnectionRepository;
use App\Models\GamePlayDisconnectionEloquentModel;
use MyDramGames\Utils\Player\Player;

class GamePlayDisconnectionRepositoryEloquent implements GamePlayDisconnectionRepository
{
    public function getOneByGamePlayAndPlayer(GamePlay $gamePlay, Player $player): ?GamePlayDisconnection
    {
        return GamePlayDisconnectionEloquentModel::where('game_play_id', '=', $gamePlay->getId())
            ->where('playerable_id', '=', $player->getId())
            ->first();
    }
}
