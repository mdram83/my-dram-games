<?php

namespace App\GameCore\GameOptionValue;

enum GameOptionValueAutostart: int implements GameOptionValue
{
    case Enabled = 1;
    case Disabled = 0;
}
