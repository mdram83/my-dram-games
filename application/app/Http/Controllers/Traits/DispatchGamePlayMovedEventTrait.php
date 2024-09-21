<?php

namespace App\Http\Controllers\Traits;

use App\Events\GameCore\GamePlay\GamePlayMovedEvent;
use MyDramGames\Core\GamePlay\GamePlay;

trait DispatchGamePlayMovedEventTrait
{
    protected function dispatchGamePlayMovedEvent(GamePlay $gamePlay): void
    {
        foreach ($gamePlay->getGameInvite()->getPlayers()->toArray() as $player) {
            GamePlayMovedEvent::dispatch($gamePlay, $player);
        }
    }
}
