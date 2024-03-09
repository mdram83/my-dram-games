<?php

namespace App\GameCore\GameSetup;

use App\GameCore\GameOption\GameOption;
use App\GameCore\GameOption\GameOptionAutostart;
use App\GameCore\GameOption\GameOptionException;
use App\GameCore\GameOption\GameOptionNumberOfPlayers;
use App\GameCore\GameOptionValue\GameOptionValue;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;

class GameSetupBase implements GameSetup
{
    protected array $options;

    /**
     * @throws GameOptionException
     */
    final public function __construct()
    {
        $this->setDefaults();
    }

    /**
     * This is only method that must be overwritten in concrete per GameBox implementations extending GameSetupBase
     * @return void
     * @throws GameOptionException
     */
    protected function setDefaults(): void
    {
        $numberOfPlayers = new GameOptionNumberOfPlayers(
            [GameOptionValueNumberOfPlayers::Players002],
            GameOptionValueNumberOfPlayers::Players002
        );

        $autostart = new GameOptionAutostart(
            [GameOptionValueAutostart::Enabled, GameOptionValueAutostart::Disabled],
            GameOptionValueAutostart::Enabled
        );

        $this->options = [
            $numberOfPlayers->getKey() => $numberOfPlayers,
            $autostart->getKey() => $autostart,
        ];
    }

    /**
     * @throws GameSetupException
     */
    final public function getOption(string $key): GameOption
    {
        if (!isset($this->options[$key])) {
            throw new GameSetupException(GameSetupException::MESSAGE_OPTION_NOT_SET);
        }

        return $this->options[$key];
    }

    /**
     * @throws GameSetupException
     */
    final public function getAllOptions(): array
    {
        $validatedOptions = [];
        foreach (array_keys($this->options) as $name) {
            $validatedOptions[$name] = $this->getOption($name);
        }
        return $validatedOptions;
    }

    /**
     * @throws GameSetupException
     */
    final public function configureOptions(array $options): void
    {
        $this->validateOptions($options);
        foreach ($options as $key => $value) {
            $this->getOption($key)->setConfiguredValue($value);
        }
    }

    final public function isConfigured(): bool
    {
        return count($this->options) === count(array_filter($this->options, fn($option) => $option->isConfigured()));
    }

    /**
     * @throws GameSetupException
     */
    final public function getNumberOfPlayers(): GameOption
    {
        return $this->getOption('numberOfPlayers');
    }

    /**
     * @throws GameSetupException
     */

    final public function getAutostart(): GameOption
    {
        return $this->getOption('autostart');
    }

    /**
     * @throws GameSetupException
     */
    final protected function validateOptions(array $options): void
    {
        foreach ($options as $key => $option) {

            if (!$this->hasProperFormat($key, $option)) {
                throw new GameSetupException(GameSetupException::MESSAGE_OPTION_INCORRECT);
            }

            if ($this->isExceedingDefaults($key, $option)) {
                throw new GameSetupException(GameSetupException::MESSAGE_OPTION_OUTSIDE);
            }
        }

        if (!$this->isCoveringDefaults($options)) {
            throw new GameSetupException(GameSetupException::MESSAGE_OPTION_NOT_SET);
        }
    }

    final protected function hasProperFormat(mixed $key, mixed $value): bool
    {
        return is_string($key) && is_a($value, GameOptionValue::class);
    }

    final protected function isExceedingDefaults(string $key, mixed $value): bool
    {
        if (!in_array($key, array_keys($this->options))) {
            return true;
        }

        if (!in_array($value, $this->options[$key]->getAvailableValues(), true)) {
            return true;
        }

        return false;
    }

    final protected function isCoveringDefaults(array $options): bool
    {
        foreach(array_keys($this->options) as $key) {
            if (!in_array($key, array_keys($options))) {
                return false;
            }
        }
        return true;
    }
}
