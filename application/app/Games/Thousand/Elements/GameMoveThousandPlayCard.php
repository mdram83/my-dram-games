<?php

namespace App\Games\Thousand\Elements;

use App\GameCore\GameElements\GameMove\GameMove;
use App\Games\Thousand\Elements\GameMoveThousand;

class GameMoveThousandPlayCard extends GameMoveThousand implements GameMove
{
    protected function isValidInput(): bool
    {
        if (!$this->hasPhase() || !$this->hasValidCard()) {
            return false;
        }

        return true;
    }

    private function hasValidCard(): bool
    {
        return
            isset($this->details['card'])
            && is_string($this->details['card'])
            && $this->details['card'] !== '';
    }
}
