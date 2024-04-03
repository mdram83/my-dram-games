<?php

namespace App\GameCore\GameOptionValue;

enum GameOptionValueForfeitAfter: int implements GameOptionValue
{
    case Disabled  = 0;
    case Minute    = 60;
    case Minutes5  = 300;
    case Minutes10 = 600;
    case Hour      = 3600;
    case Day       = 86400;
}
