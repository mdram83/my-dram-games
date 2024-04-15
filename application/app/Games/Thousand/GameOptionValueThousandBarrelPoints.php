<?php

namespace App\Games\Thousand;

use App\GameCore\GameOptionValue\GameOptionValue;
use App\GameCore\GameOptionValue\PhpEnum\GameOptionValuePhpEnumBackedTrait;

enum GameOptionValueThousandBarrelPoints: int implements GameOptionValue
{
    use GameOptionValuePhpEnumBackedTrait;

    case Disabled = 0;
    case EightHundred = 800;
    case EightHundredEighty = 880;
    case NineHundred = 900;

    public function getLabel(): string
    {
        return match($this) {
            self::Disabled => 'Disabled',
            self::EightHundred => 'Eight Hundred',
            self::EightHundredEighty => 'Eight Hundred Eighty',
            self::NineHundred => 'Nine Hundred',
        };
    }
}
