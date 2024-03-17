<?php

namespace App\GameCore\GameSetup;

use App\GameCore\GameOption\GameOption;
use App\GameCore\GameOption\GameOptionAutostart;
use App\GameCore\GameOption\GameOptionException;
use App\GameCore\GameOption\GameOptionNumberOfPlayers;
use App\GameCore\GameOptionValue\GameOptionValue;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\Services\Collection\Collection;
use App\GameCore\Services\Collection\CollectionGameOption;

class GameSetupBase implements GameSetup
{
    protected CollectionGameOption $options;

    /**
     * @throws GameOptionException
     */
    final public function __construct(Collection $optionsHandler)
    {
        $this->setDefaults($optionsHandler);
    }

    /**
     * This is only method that must be overwritten in concrete per GameBox implementations extending GameSetupBase
     * @param Collection $optionsHandler
     * @return void
     * @throws GameOptionException
     */
    protected function setDefaults(Collection $optionsHandler): void
    {
        $numberOfPlayers = new GameOptionNumberOfPlayers(
            [GameOptionValueNumberOfPlayers::Players002],
            GameOptionValueNumberOfPlayers::Players002
        );

        $autostart = new GameOptionAutostart(
            [GameOptionValueAutostart::Enabled, GameOptionValueAutostart::Disabled],
            GameOptionValueAutostart::Enabled
        );

        $this->options = new CollectionGameOption($optionsHandler, [$numberOfPlayers, $autostart]);
    }

    /**
     * @throws GameSetupException
     */
    final public function getOption(string $key): GameOption
    {
        if (!$this->options->exist($key)) {
            throw new GameSetupException(GameSetupException::MESSAGE_OPTION_NOT_SET);
        }

        return $this->options->getOne($key);
    }

    final public function getAllOptions(): array
    {
        return $this->options->toArray();
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
        return $this->options->count() === $this->options->filter(fn($option) => $option->isConfigured())->count();
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
        if (!$this->options->exist($key)) {
            return true;
        }

        if (!in_array($value, $this->options->getOne($key)->getAvailableValues(), true)) {
            return true;
        }

        return false;
    }

    final protected function isCoveringDefaults(array $options): bool
    {
        foreach(array_keys($this->options->toArray()) as $key) {
            if (!in_array($key, array_keys($options))) {
                return false;
            }
        }

        return true;
    }
}
