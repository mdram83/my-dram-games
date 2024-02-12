<?php

namespace App\Events\GameCore\GamePlay;

use App\Broadcasting\GameCore\Game\GameChannel;
use App\Models\GameCore\Game\Game;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GamePlayStartedEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    private int|string $gameId;

    public function __construct(Game $game)
    {
        $this->gameId = $game->getId();
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel(GameChannel::CHANNEL_ROUTE_PREFIX . $this->gameId)
        ];
    }
}
