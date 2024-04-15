<?php

namespace App\GameCore\GameOptionValue;

use App\GameCore\GameOptionValue\PhpEnum\GameOptionValuePhpEnumBackedTrait;

enum GameOptionValueAutostart: int implements GameOptionValue
{
    use GameOptionValuePhpEnumBackedTrait;

    case Enabled = 1;
    case Disabled = 0;

    public function getLabel(): string
    {
        return match($this) {
            self::Enabled => 'Enabled',
            self::Disabled => 'Disabled',
        };
    }
}
