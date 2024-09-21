<?php

namespace App\Broadcasting;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\App;
use MyDramGames\Core\GamePlay\GamePlayRepository;
use MyDramGames\Utils\Player\Player;

class GamePlayPlayerChannel
{
    public const string CHANNEL_ROUTE_PREFIX = 'game-play-player.';
    public const string CHANNEL_ROUTE_PARAM = '{gamePlayId}.{playerId}';

    public static function getRouteName(): string
    {
        return static::CHANNEL_ROUTE_PREFIX . static::CHANNEL_ROUTE_PARAM;
    }

    public function join(Authenticatable|Player $player, int|string $gamePlayId, int|string $playerId): array|bool
    {
        if ($player->getId() !== $playerId) {
            return false;
        }

        $gamePlay = App::make(GamePlayRepository::class)->getOne($gamePlayId);
        $players = $gamePlay->getPlayers();

        return $players->exist($player->getId());
    }
}
