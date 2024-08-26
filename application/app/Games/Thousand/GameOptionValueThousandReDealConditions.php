<?php

namespace App\Games\Thousand;

use App\GameCore\GameOptionValue\GameOptionValue;
use MyDramGames\Utils\Php\Enum\GetValueBackedEnumTrait;

enum GameOptionValueThousandReDealConditions: string implements GameOptionValue
{
    use GetValueBackedEnumTrait;

    case Disabled = 'Disabled';
    case FourNines = 'Four Nines';
    case TenPoints = 'Ten Points';
    case EighteenPoints = 'Eighteen Points';

    public function getLabel(): string
    {
        return $this->getValue();
    }
}
