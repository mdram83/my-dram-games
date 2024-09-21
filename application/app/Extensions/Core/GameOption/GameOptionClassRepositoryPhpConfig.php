<?php

namespace App\Extensions\Core\GameOption;

use Illuminate\Support\Facades\Config;
use MyDramGames\Core\Exceptions\GameOptionException;

class GameOptionClassRepositoryPhpConfig implements GameOptionClassRepository
{
    /**
     * @throws GameOptionException
     */
    public function getOptionClassname(string $optionKey): string
    {
        return $this->getFromConfig($optionKey, 'option');
    }

    /**
     * @throws GameOptionException
     */
    public function getValueClassname(string $optionKey): string
    {
        return $this->getFromConfig($optionKey, 'value');
    }

    /**
     * @throws GameOptionException
     */
    public function getFromConfig(string $optionKey, string $configKey): string
    {
        if (!$configValue = Config::get("game-options.$optionKey.$configKey")) {
            throw new GameOptionException(GameOptionException::MESSAGE_INCOMPATIBLE_VALUE);
        }
        return $configValue;
    }
}
