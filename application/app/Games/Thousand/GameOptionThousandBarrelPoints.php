<?php

namespace App\Games\Thousand;

use App\GameCore\GameOption\GameOptionBase;
use App\GameCore\GameOption\GameOptionException;
use App\GameCore\GameOptionType\GameOptionType;
use App\GameCore\GameOptionType\GameOptionTypeEnum;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;

class GameOptionThousandBarrelPoints extends GameOptionBase
{
    protected const KEY = 'thousand-barrel-points';
    protected const NAME = 'Barrel Points';
    protected const DESCRIPTION = 'Number of points after which player needs to win bidding to get any points added.';
    protected const GAME_OPTION_VALUE_CLASS = GameOptionValueThousandBarrelPoints::class;

    protected GameOptionType $type = GameOptionTypeEnum::Radio;

    /**
     * @throws GameOptionException
     */
    public function __construct(array $availableGameOptionValues, GameOptionValueThousandBarrelPoints $defaultValue)
    {
        parent::__construct($availableGameOptionValues, $defaultValue);
    }
}
