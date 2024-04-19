<?php

namespace App\Games\Thousand\Elements;

use App\GameCore\GameElements\GameMove\GameMove;
use App\Games\Thousand\Elements\GameMoveThousand;

class GameMoveThousandSorting extends GameMoveThousand implements GameMove
{
    protected function isValidInput(): bool
    {
        if ($this->hasPhase() || !$this->isDetailsArrayOfStrings() || !$this->validateDetailsCount()) {
            return false;
        }

        return true;
    }

    private function isDetailsArrayOfStrings(): bool
    {
        $notStrings = array_filter($this->details, fn($element) => !is_string($element));
        return !(count($notStrings) > 0);
    }

    private function validateDetailsCount(): bool
    {
        return count($this->details) <= 11 && count($this->details) > 1;
    }
}
