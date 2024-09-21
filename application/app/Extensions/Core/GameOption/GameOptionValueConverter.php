<?php

namespace App\Extensions\Core\GameOption;

use MyDramGames\Core\GameOption\GameOptionValue;

interface GameOptionValueConverter
{
    public function convert(mixed $value, string $gameOptionKey): GameOptionValue;
}
