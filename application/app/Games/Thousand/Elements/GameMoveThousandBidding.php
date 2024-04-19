<?php

namespace App\Games\Thousand\Elements;

use App\GameCore\GameElements\GameMove\GameMove;
use App\Games\Thousand\Elements\GameMoveThousand;

class GameMoveThousandBidding extends GameMoveThousand implements GameMove
{
    protected function isValidInput(): bool
    {
        if (
            !$this->hasPhase()
            || !$this->hasValidBiddingDecision()
            || ($this->isBidding() && !$this->hasValidBiddingAmount())
        ) {
            return false;
        }

        return true;
    }

    private function hasValidBiddingDecision(): bool
    {
        return isset($this->details['decision']) && in_array($this->details['decision'], ['bid', 'pass'], true);
    }

    private function isBidding(): bool
    {
        return $this->details['decision'] === 'bid';
    }

    private function hasValidBiddingAmount(): bool
    {
        return isset($this->details['bidAmount']) && is_int($this->details['bidAmount']);
    }
}
