<?php

namespace App\Broadcasting;

use App\GameCore\GameInvite\GameInviteRepository;
use App\GameCore\GamePlay\GamePlayRepository;
use App\GameCore\Player\Player;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\App;

class GamePlayPlayersChannel
{
    public const CHANNEL_ROUTE_PREFIX = 'game-play-players.';
    public const CHANNEL_ROUTE_PARAM = '{gameId}';

    public static function getRouteName(): string
    {
        return static::CHANNEL_ROUTE_PREFIX . static::CHANNEL_ROUTE_PARAM;
    }

    public function join(Authenticatable|Player $player, int|string $gamePlayId): array|bool
    {
        $gamePlay = App::make(GamePlayRepository::class)->getOne($gamePlayId);

        if ($gamePlay->getGameInvite()->isPlayerAdded($player)) {
            return ['name' => $player->getName()];
        }

        return false;
    }
}
