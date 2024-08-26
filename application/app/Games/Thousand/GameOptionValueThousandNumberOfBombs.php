<?php

namespace App\Games\Thousand;

use App\GameCore\GameOptionValue\GameOptionValue;
use MyDramGames\Utils\Php\Enum\GetValueBackedEnumTrait;

enum GameOptionValueThousandNumberOfBombs: int implements GameOptionValue
{
    use GetValueBackedEnumTrait;

    case Disabled = 0;
    case One = 1;
    case Two = 2;

    public function getLabel(): string
    {
        return match($this) {
            self::Disabled => 'Disabled',
            self::One => 'One Bomb',
            self::Two => 'Two Bombs',
        };
    }
}
