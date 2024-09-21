<?php

namespace App\Extensions\Core\GameOption;

interface GameOptionClassRepository
{
    public function getOptionClassname(string $optionKey): string;
    public function getValueClassname(string $optionKey): string;
}
