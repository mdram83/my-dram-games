<?php

namespace App\Events\GameCore\GamePlay;

use App\Broadcasting\GameCore\Game\GameChannel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GamePlayStartedEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public string $gamePlayUrl;

     public function __construct(int|string $gameId)
    {
        $this->gamePlayUrl = route('play', $gameId);
    }

    public function broadcastOn(): array|Channel
    {
        return new GameChannel();
    }
}
