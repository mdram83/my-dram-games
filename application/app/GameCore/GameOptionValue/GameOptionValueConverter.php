<?php

namespace App\GameCore\GameOptionValue;

interface GameOptionValueConverter
{
    public function convert(mixed $value, string $gameOptionKey): GameOptionValue;
}
