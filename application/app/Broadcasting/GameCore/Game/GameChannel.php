<?php

namespace App\Broadcasting\GameCore\Game;

use App\Models\GameCore\Game\GameRepository;
use App\Models\User;
use Illuminate\Support\Facades\App;

class GameChannel
{
    public const CHANNEL_ROUTE_PREFIX = 'game.';
    public const CHANNEL_ROUTE_PARAM = '{gameId}';

    public static function getRouteName(): string
    {
        return static::CHANNEL_ROUTE_PREFIX . static::CHANNEL_ROUTE_PARAM;
    }

    public function join(User $user, int|string $gameId): array|bool
    {
        if (App::make(GameRepository::class)->getOne($gameId)->isPlayerAdded($user)) {
            return ['name' => $user->getName()];
        }
        return false;
    }
}
