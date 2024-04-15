<?php

namespace App\Games\Thousand;

use App\GameCore\GameOption\GameOptionBase;
use App\GameCore\GameOption\GameOptionException;
use App\GameCore\GameOptionType\GameOptionType;
use App\GameCore\GameOptionType\GameOptionTypeEnum;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;

class GameOptionThousandNumberOfBombs extends GameOptionBase
{
    protected const KEY = 'thousand-number-of-bombs';
    protected const NAME = 'Number Of Bombs';
    protected const DESCRIPTION = 'Number of Bomb moves available for player winning bid at 100 points.';
    protected const GAME_OPTION_VALUE_CLASS = GameOptionValueThousandNumberOfBombs::class;

    protected GameOptionType $type = GameOptionTypeEnum::Radio;

    /**
     * @throws GameOptionException
     */
    public function __construct(array $availableGameOptionValues, GameOptionValueThousandNumberOfBombs $defaultValue)
    {
        parent::__construct($availableGameOptionValues, $defaultValue);
    }
}
