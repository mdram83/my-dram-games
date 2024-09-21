<?php

namespace App\Broadcasting;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\App;
use MyDramGames\Core\GamePlay\GamePlayRepository;
use MyDramGames\Utils\Player\Player;

class GamePlayPlayersChannel
{
    public const string CHANNEL_ROUTE_PREFIX = 'game-play-players.';
    public const string CHANNEL_ROUTE_PARAM = '{gameId}';

    public static function getRouteName(): string
    {
        return static::CHANNEL_ROUTE_PREFIX . static::CHANNEL_ROUTE_PARAM;
    }

    public function join(Authenticatable|Player $player, int|string $gamePlayId): array|bool
    {
        $gamePlay = App::make(GamePlayRepository::class)->getOne($gamePlayId);

        if ($gamePlay->getGameInvite()->isPlayer($player)) {
            return ['name' => $player->getName()];
        }

        return false;
    }
}
