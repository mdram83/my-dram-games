<?php

namespace App\GameCore\GameOption;

interface GameOptionClassRepository
{
    public function getOptionClassname(string $optionKey): string;
    public function getValueClassname(string $optionKey): string;
}
