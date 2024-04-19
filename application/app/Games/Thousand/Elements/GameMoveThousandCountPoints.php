<?php

namespace App\Games\Thousand\Elements;

use App\GameCore\GameElements\GameMove\GameMove;
use App\Games\Thousand\Elements\GameMoveThousand;

class GameMoveThousandCountPoints extends GameMoveThousand implements GameMove
{
    protected function isValidInput(): bool
    {
        if (!$this->hasPhase() || !$this->hasReadyFlagSetToTrue()) {
            return false;
        }

        return true;
    }

    private function hasReadyFlagSetToTrue(): bool
    {
        return (isset($this->details['ready']) && $this->details['ready'] === true);
    }
}
