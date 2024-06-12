<?php

namespace App\Games\Thousand\Elements;

use App\GameCore\GameElements\GameMove\GameMove;
use App\Games\Thousand\Elements\GameMoveThousand;

class GameMoveThousandCollectTricks extends GameMoveThousand implements GameMove
{
    protected function isValidInput(): bool
    {
        if (!$this->hasPhase() || !$this->hasCollectFlagSetToTrue()) {
            return false;
        }

        return true;
    }

    private function hasCollectFlagSetToTrue(): bool
    {
        return (isset($this->details['collect']) && $this->details['collect'] === true);
    }
}
