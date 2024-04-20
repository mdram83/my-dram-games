<?php

namespace App\Games\Thousand\Elements;

use App\GameCore\GameElements\GameMove\GameMove;
use App\Games\Thousand\Elements\GameMoveThousand;

class GameMoveThousandStockDistribution extends GameMoveThousand implements GameMove
{
    protected function isValidInput(): bool
    {
        if (!$this->hasPhase() || !$this->hasValidDataStructure()) {
            return false;
        }

        return true;
    }

    private function hasValidDataStructure(): bool
    {
        return
            count($this->details) === 2
            && $this->hasStringElements(array_keys($this->details))
            && $this->hasStringElements(array_values($this->details));
    }

    private function hasStringElements(array $array): bool
    {
        return count($array) === count(array_filter($array, fn($element) => is_string($element)));
    }
}
