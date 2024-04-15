<?php

namespace App\Games\Thousand;

use App\GameCore\GameOptionValue\GameOptionValue;
use App\GameCore\GameOptionValue\PhpEnum\GameOptionValuePhpEnumBackedTrait;

enum GameOptionValueThousandNumberOfBombs: int implements GameOptionValue
{
    use GameOptionValuePhpEnumBackedTrait;

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
