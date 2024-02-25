<?php

namespace App\GameCore\GameSetup;

class GameSetupBase implements GameSetup
{
    protected array $options = [
        'numberOfPlayers' => null,
        'autostart' => [false, true],
    ];

    final public function __construct()
    {
        $this->setDefaults();
    }

    /**
     * This is only method that must be overwritten in concrete per GameBox implementations extending GameSetupBase
     * @return void
     */
    protected function setDefaults(): void
    {
        $this->options = [
            'numberOfPlayers' => [2],
            'autostart' => [false, true],
        ];
    }

    /**
     * @throws GameSetupException
     */
    final public function getOption(string $name): array
    {
        if (!isset($this->options[$name])) {
            throw new GameSetupException(GameSetupException::MESSAGE_OPTION_NOT_SET);
        }

        return $this->options[$name];
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
    final public function setOptions(array $options): void
    {
        $this->validateOptions($options);
        foreach ($options as $name => $value) {
            $this->options[$name] = [$value];
        }
    }

    final public function isConfigured(): bool
    {
        return count($this->options) === count(array_filter($this->options, fn($option) => count($option) === 1));
    }

    /**
     * @throws GameSetupException
     */
    final public function getNumberOfPlayers(): array
    {
        return $this->getOption('numberOfPlayers');
    }

    /**
     * @throws GameSetupException
     */

    final public function getAutostart(): array
    {
        return $this->getOption('autostart');
    }

    /**
     * @throws GameSetupException
     */
    final protected function validateOptions(array $options): void
    {
        foreach ($options as $name => $value) {

            if (!$this->hasProperFormat($name, $value)) {
                throw new GameSetupException(GameSetupException::MESSAGE_OPTION_INCORRECT);
            }

            if ($this->isExceedingDefaults($name, $value)) {
                throw new GameSetupException(GameSetupException::MESSAGE_OPTION_OUTSIDE);
            }
        }

        if (!$this->isCoveringDefaults($options)) {
            throw new GameSetupException(GameSetupException::MESSAGE_OPTION_NOT_SET);
        }
    }

    final protected function hasProperFormat(mixed $key, mixed $value): bool
    {
        return is_string($key) && !is_array($value);
    }

    final protected function isExceedingDefaults(string $key, mixed $value): bool
    {
        if (!in_array($key, array_keys($this->options)) || !in_array($value, $this->options[$key], true)) {
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
