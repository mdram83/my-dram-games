<?php

namespace App\GameCore\GameOption;

use App\GameCore\GameOptionType\GameOptionType;
use App\GameCore\GameOptionValue\GameOptionValue;

abstract class GameOptionBase implements GameOption
{
    protected array $availableValues;
    protected GameOptionValue $defaultValue;
    protected GameOptionValue $configuredValue;

    protected const KEY = null;
    protected const NAME = null;
    protected const DESCRIPTION = null;
    protected const GAME_OPTION_VALUE_CLASS = null;

    protected GameOptionType $type;

    final public function getKey(): string
    {
        return $this::KEY;
    }

    final public function getName(): string
    {
        return $this::NAME;
    }

    final public function getDescription(): string
    {
        return $this::DESCRIPTION;
    }

    final public function getType(): GameOptionType
    {
        return $this->type;
    }

    final public static function getOptionValueClass(): string
    {
        return static::GAME_OPTION_VALUE_CLASS;
    }

    final public function getDefaultValue(): GameOptionValue
    {
        return $this->defaultValue;
    }

    final public function getAvailableValues(): array
    {
        return $this->availableValues;
    }

    /**
     * @throws GameOptionException
     */
    public function getConfiguredValue(): GameOptionValue
    {
        if (!isset($this->configuredValue)) {
            throw new GameOptionException(GameOptionException::MESSAGE_NOT_CONFIGURED);
        }
        return $this->configuredValue;
    }

    /**
     * @throws GameOptionException
     */
    public function setConfiguredValue(GameOptionValue $value): void
    {
        if (isset($this->configuredValue)) {
            throw new GameOptionException(GameOptionException::MESSAGE_ALREADY_CONFIGURED);
        }
        $this->configuredValue = $value;
    }

    final public function isConfigured(): bool
    {
        return isset($this->configuredValue);
    }

    final protected function hasValidGameOptionValues(array $values): bool
    {
        if (count($values) === 0) {
            return false;
        }

        foreach ($values as $value) {
            if (!is_a($value, $this->getOptionValueClass())) {
                return false;
            }
        }

        return true;
    }
}
