<?php

namespace App\Games\Thousand;

use App\GameCore\GameOptionValue\GameOptionValue;

enum GameOptionValueThousandBarrelPoints: int implements GameOptionValue
{
    case Disabled = 0;
    case EightHundred = 800;
    case EightHundredEighty = 880;
    case NineHundred = 900;
}
