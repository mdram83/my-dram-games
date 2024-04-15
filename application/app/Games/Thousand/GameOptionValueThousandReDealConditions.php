<?php

namespace App\Games\Thousand;

use App\GameCore\GameOptionValue\GameOptionValue;

enum GameOptionValueThousandReDealConditions: string implements GameOptionValue
{
    case Disabled = 'Disabled';
    case FourNines = 'Four Nines';
    case TenPoints = 'Ten Points';
    case EighteenPoints = 'Eighteen Points';
}
