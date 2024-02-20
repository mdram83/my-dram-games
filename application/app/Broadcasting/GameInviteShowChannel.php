<?php

namespace App\Broadcasting;

use App\GameCore\GameInvite\GameInviteRepository;
use App\GameCore\Player\Player;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\App;

class GameInviteShowChannel
{
    public const CHANNEL_ROUTE_PREFIX = 'game-invite.';
    public const CHANNEL_ROUTE_PARAM = '{gameId}';

    public static function getRouteName(): string
    {
        return static::CHANNEL_ROUTE_PREFIX . static::CHANNEL_ROUTE_PARAM;
    }

    public function join(Authenticatable|Player $player, int|string $gameId): array|bool
    {
        $game = App::make(GameInviteRepository::class)->getOne($gameId);

        if ($game->isPlayerAdded($player)) {
            return ['name' => $player->getName()];
        }

        return false;
    }
}
