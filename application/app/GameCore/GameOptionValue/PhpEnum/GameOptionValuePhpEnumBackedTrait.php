<?php

namespace App\GameCore\GameOptionValue\PhpEnum;

trait GameOptionValuePhpEnumBackedTrait
{
    public function getValue(): int|string|null
    {
        return $this->value;
    }
}
