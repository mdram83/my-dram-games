<?php

namespace App\GameCore\GameOption;

use App\GameCore\GameOptionType\GameOptionType;
use App\GameCore\GameOptionValue\GameOptionValue;

interface GameOption
{
    public function getKey(): string;
    public function getName(): string;
    public function getDescription(): string;
    public function getType(): GameOptionType;
    public static function getOptionValueClass(): string;
    public function getDefaultValue(): GameOptionValue;
    public function getAvailableValues(): array;
    public function getConfiguredValue(): GameOptionValue;
    public function setConfiguredValue(GameOptionValue $value): void;
    public function isConfigured(): bool;
}
