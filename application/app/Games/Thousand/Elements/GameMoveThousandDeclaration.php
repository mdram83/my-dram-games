<?php

namespace App\Games\Thousand\Elements;

use App\GameCore\GameElements\GameMove\GameMove;
use App\Games\Thousand\Elements\GameMoveThousand;

class GameMoveThousandDeclaration extends GameMoveThousand implements GameMove
{
    protected function isValidInput(): bool
    {
        if (!$this->hasPhase() || !$this->hasValidDeclaration()) {
            return false;
        }

        return true;
    }

    private function hasValidDeclaration(): bool
    {
        return isset($this->details['declaration']) && is_int($this->details['declaration']);
    }
}
