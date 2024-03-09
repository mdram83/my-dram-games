<?php

namespace App\GameCore\GameOption;

use App\GameCore\GameOptionType\GameOptionType;
use App\GameCore\GameOptionType\GameOptionTypeEnum;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;

class GameOptionNumberOfPlayers extends GameOptionBase
{
    protected const KEY = 'numberOfPlayers';
    protected const NAME = 'Number of players';
    protected const DESCRIPTION = 'How many players you want to play with';
    protected const GAME_OPTION_VALUE_CLASS = GameOptionValueNumberOfPlayers::class;

    protected GameOptionType $type = GameOptionTypeEnum::Radio;

    /**
     * @throws GameOptionException
     */
    public function __construct(array $availableGameOptionValues, GameOptionValueNumberOfPlayers $defaultValue)
    {
        if (!$this->hasValidGameOptionValues($availableGameOptionValues)) {
            throw new GameOptionException(GameOptionException::MESSAGE_INCORRECT_AVAILABLE);
        }
        $this->availableValues = $availableGameOptionValues;
        $this->defaultValue = $defaultValue;
    }


}
