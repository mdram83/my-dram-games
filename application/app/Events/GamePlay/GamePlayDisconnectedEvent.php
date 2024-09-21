<?php

namespace App\Events\GamePlay;

use App\Broadcasting\GamePlayPlayerChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MyDramGames\Core\GamePlay\GamePlay;
use MyDramGames\Utils\Player\Player;

class GamePlayDisconnectedEvent implements ShouldBroadcast
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

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel($this->channelId),
        ];
    }
}
