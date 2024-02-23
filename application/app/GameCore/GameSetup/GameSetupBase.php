<?php

namespace App\GameCore\GameSetup;

class GameSetupBase implements GameSetup
{
    protected array $options = [
        'numberOfPlayers' => null,
        'autostart' => [false, true],
    ];

    /**
     * @throws GameSetupException
     */
    final public function __construct(array $options = [])
    {
        $this->setDefaults();
        $this->validateOptions($options);
        $this->options = array_merge($this->options, $options);
    }

    /**
     * This is only method that must be overwritten in concrete per GameBox implementations extending GameSetupBase
     * @return void
     */
    protected function setDefaults(): void
    {
        $this->options = [
            'numberOfPlayers' => null,
            'autostart' => [false, true],
        ];
    }

    /**
     * @throws GameSetupException
     */
    final protected function validateOptions(array $options): void
    {
        foreach ($options as $name => $option) {

            if (!$this->hasProperFormat($name, $option)) {
                throw new GameSetupException(GameSetupException::MESSAGE_OPTION_INCORRECT);
            }

            if ($this->exceedDefault($name, $option)) {
                throw new GameSetupException(GameSetupException::MESSAGE_OPTION_OUTSIDE);
            }
        }
    }

    final protected function hasProperFormat(mixed $key, mixed $option): bool
    {
        return is_string($key) && is_array($option);
    }

    final protected function exceedDefault(string $key, array $option): bool
    {
        if (in_array($key, array_keys($this->options))) {
            foreach ($option as $value) {
                if (!in_array($value, $this->options[$key], true)) {
                    return true;
                }
            }
        }
        return false;
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
}
