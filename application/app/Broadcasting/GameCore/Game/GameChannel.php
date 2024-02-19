<?php

namespace App\Broadcasting\GameCore\Game;

use App\Models\GameCore\Game\GameRepository;
use App\Models\GameCore\Player\Player;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\App;

class GameChannel
{
    public const CHANNEL_ROUTE_PREFIX = 'game.';
    public const CHANNEL_ROUTE_PARAM = '{gameId}';

    public static function getRouteName(): string
    {
        return static::CHANNEL_ROUTE_PREFIX . static::CHANNEL_ROUTE_PARAM;
    }

    public function join(Authenticatable|Player $player, int|string $gameId): array|bool
    {
        $game = App::make(GameRepository::class)->getOne($gameId);

        if ($game->isPlayerAdded($player)) {
            return ['name' => $player->getName()];
        }

        return false;
    }
}
