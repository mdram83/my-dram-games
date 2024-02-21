<?php

namespace App\Events\GameCore\GamePlay;

use App\Broadcasting\GameInvitePlayersChannel;
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

    private int|string $gameInviteId;

    public function __construct(GameInvite $gameInvite)
    {
        $this->gameInviteId = $gameInvite->getId();
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel(GameInvitePlayersChannel::CHANNEL_ROUTE_PREFIX . $this->gameInviteId)
        ];
    }
}
