<?php

namespace App\Games\Thousand;

use App\GameCore\GameOptionValue\GameOptionValue;
use MyDramGames\Utils\Php\Enum\GetValueBackedEnumTrait;

enum GameOptionValueThousandBarrelPoints: int implements GameOptionValue
{
    use GetValueBackedEnumTrait;

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
