<?php

namespace App\GameCore\GameOption;

use App\GameCore\GameOptionType\GameOptionType;
use App\GameCore\GameOptionType\GameOptionTypeEnum;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;

class GameOptionAutostart extends GameOptionBase
{
    protected const KEY = 'autostart';
    protected const NAME = 'Autostart';
    protected const DESCRIPTION = 'Start game automatically when all players are ready';
    protected const GAME_OPTION_VALUE_CLASS = GameOptionValueAutostart::class;

    protected GameOptionType $type = GameOptionTypeEnum::Checkbox;

    /**
     * @throws GameOptionException
     */
    public function __construct(array $availableGameOptionValues, GameOptionValueAutostart $defaultValue)
    {
        if (!$this->hasValidGameOptionValues($availableGameOptionValues)) {
            throw new GameOptionException(GameOptionException::MESSAGE_INCORRECT_AVAILABLE);
        }
        $this->availableValues = $availableGameOptionValues;
        $this->defaultValue = $defaultValue;
    }


}
