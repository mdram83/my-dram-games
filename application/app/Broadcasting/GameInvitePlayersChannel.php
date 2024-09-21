<?php

namespace App\Broadcasting;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\App;
use MyDramGames\Core\GameInvite\GameInviteRepository;
use MyDramGames\Utils\Player\Player;

class GameInvitePlayersChannel
{
    public const string CHANNEL_ROUTE_PREFIX = 'game-invite-players.';
    public const string CHANNEL_ROUTE_PARAM = '{gameId}';

    public static function getRouteName(): string
    {
        return static::CHANNEL_ROUTE_PREFIX . static::CHANNEL_ROUTE_PARAM;
    }

    public function join(Authenticatable|Player $player, int|string $gameInviteId): array|bool
    {
        $gameInvite = App::make(GameInviteRepository::class)->getOne($gameInviteId);

        if ($gameInvite->isPlayer($player)) {
            return ['name' => $player->getName()];
        }

        return false;
    }
}
