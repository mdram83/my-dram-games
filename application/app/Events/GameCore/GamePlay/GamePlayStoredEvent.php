<?php

namespace App\Events\GameCore\GamePlay;

use App\Broadcasting\GameInvitePlayersChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MyDramGames\Core\GameInvite\GameInvite;
use MyDramGames\Core\GamePlay\GamePlay;

class GamePlayStoredEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    private int|string $gameInviteId;
    private int|string $gamePlayId;

    public function __construct(GameInvite $gameInvite, GamePlay $gamePlay)
    {
        $this->gameInviteId = $gameInvite->getId();
        $this->gamePlayId = $gamePlay->getId();
    }

    public function broadcastWith(): array
    {
        return ['gamePlayId' => $this->gamePlayId];
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel(GameInvitePlayersChannel::CHANNEL_ROUTE_PREFIX . $this->gameInviteId)
        ];
    }
}
