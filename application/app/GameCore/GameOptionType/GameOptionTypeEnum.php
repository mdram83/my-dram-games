<?php

namespace App\GameCore\GameOptionType;

enum GameOptionTypeEnum: string implements GameOptionType
{
    case Radio = 'radio';
    case Select = 'select';
    case Checkbox = 'checkbox';
}
