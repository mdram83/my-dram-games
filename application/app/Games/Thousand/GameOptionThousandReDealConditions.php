<?php

namespace App\Games\Thousand;

use App\GameCore\GameOption\GameOptionBase;
use App\GameCore\GameOption\GameOptionException;
use App\GameCore\GameOptionType\GameOptionType;
use App\GameCore\GameOptionType\GameOptionTypeEnum;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;

class GameOptionThousandReDealConditions extends GameOptionBase
{
    protected const KEY = 'thousand-re-deal-conditions';
    protected const NAME = 'Re-Deal Conditions';
    protected const DESCRIPTION = 'Conditions that allow player to request re-deal if poor cards at hand after dealing.';
    protected const GAME_OPTION_VALUE_CLASS = GameOptionValueThousandReDealConditions::class;

    protected GameOptionType $type = GameOptionTypeEnum::Radio;

    /**
     * @throws GameOptionException
     */
    public function __construct(array $availableGameOptionValues, GameOptionValueThousandReDealConditions $defaultValue)
    {
        parent::__construct($availableGameOptionValues, $defaultValue);
    }
}
