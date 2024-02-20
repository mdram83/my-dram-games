<?php

namespace App\Events\GameCore\GamePlay;

use App\Broadcasting\GameInviteShowChannel;
use App\GameCore\GameInvite\GameInvite;
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

    public function __construct(GameInvite $game)
    {
        $this->gameId = $game->getId();
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel(GameInviteShowChannel::CHANNEL_ROUTE_PREFIX . $this->gameId)
        ];
    }
}
