<?php

namespace App\Games\Thousand;

use App\GameCore\GameOptionValue\GameOptionValue;
use App\GameCore\GameOptionValue\PhpEnum\GameOptionValuePhpEnumBackedTrait;

enum GameOptionValueThousandReDealConditions: string implements GameOptionValue
{
    use GameOptionValuePhpEnumBackedTrait;

    case Disabled = 'Disabled';
    case FourNines = 'Four Nines';
    case TenPoints = 'Ten Points';
    case EighteenPoints = 'Eighteen Points';

    public function getLabel(): string
    {
        return $this->getValue();
    }
}
