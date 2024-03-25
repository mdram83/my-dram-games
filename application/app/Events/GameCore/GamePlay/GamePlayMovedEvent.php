<?php

namespace App\Events\GameCore\GamePlay;

use App\Broadcasting\GamePlayPlayerChannel;
use App\GameCore\GamePlay\GamePlay;
use App\GameCore\Player\Player;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GamePlayMovedEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    private string $channelId;

    public function __construct(
        readonly private GamePlay $gamePlay,
        readonly private Player $player
    )
    {
        $this->channelId =
            GamePlayPlayerChannel::CHANNEL_ROUTE_PREFIX
            . $this->gamePlay->getId() . '.' . $this->player->getId();
    }

    public function broadcastWith(): array
    {
        return ['situation' => $this->gamePlay->getSituation($this->player)];
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel($this->channelId),
        ];
    }
}
