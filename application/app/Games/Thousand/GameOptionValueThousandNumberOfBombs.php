<?php

namespace App\Games\Thousand;

use App\GameCore\GameOptionValue\GameOptionValue;

enum GameOptionValueThousandNumberOfBombs: int implements GameOptionValue
{
    case Disabled = 0;
    case One = 1;
    case Two = 2;
}
