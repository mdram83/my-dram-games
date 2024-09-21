<?php

namespace App\Services\GamePlayDisconnection\Eloquent;

use App\Services\GamePlayDisconnection\GamePlayDisconnection;
use App\Services\GamePlayDisconnection\GamePlayDisconnectionRepository;
use App\Models\GamePlayDisconnectionEloquentModel;
use MyDramGames\Core\GamePlay\GamePlay;
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
