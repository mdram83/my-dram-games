<?php

namespace App\GameCore\GameOption;

use App\GameCore\GameOptionType\GameOptionType;
use App\GameCore\GameOptionType\GameOptionTypeEnum;
use App\GameCore\GameOptionValue\GameOptionValueForfeitAfter;

class GameOptionForfeitAfter extends GameOptionBase
{
    protected const KEY = 'forfeitAfter';
    protected const NAME = 'Forfeit After Disconnection';
    protected const DESCRIPTION = 'Forfeit the game specific time after player disconnects during game play.';
    protected const GAME_OPTION_VALUE_CLASS = GameOptionValueForfeitAfter::class;

    protected GameOptionType $type = GameOptionTypeEnum::Radio;

    /**
     * @throws GameOptionException
     */
    public function __construct(array $availableGameOptionValues, GameOptionValueForfeitAfter $defaultValue)
    {
        parent::__construct($availableGameOptionValues, $defaultValue);
    }
}
