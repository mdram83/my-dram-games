<?php

namespace App\GameCore\GameOptionValue;

use MyDramGames\Core\GameOption\GameOptionValue;

interface GameOptionValueConverter
{
    public function convert(mixed $value, string $gameOptionKey): GameOptionValue;
}
