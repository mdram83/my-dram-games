<?php

namespace Tests\Feature\Games\Thousand;

use App\GameCore\GameOption\GameOptionAutostart;
use App\GameCore\GameOption\GameOptionForfeitAfter;
use App\GameCore\GameOption\GameOptionNumberOfPlayers;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueForfeitAfter;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\Services\Collection\Collection;
use App\Games\Thousand\GameOptionThousandBarrelPoints;
use App\Games\Thousand\GameOptionThousandNumberOfBombs;
use App\Games\Thousand\GameOptionThousandReDealConditions;
use App\Games\Thousand\GameOptionValueThousandBarrelPoints;
use App\Games\Thousand\GameOptionValueThousandNumberOfBombs;
use App\Games\Thousand\GameOptionValueThousandReDealConditions;
use App\Games\Thousand\GameSetupThousand;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class GameSetupThousandTest extends TestCase
{
    protected array $defaults;

    public function setUp(): void
    {
        parent::setUp();

        $numberOfPlayers = new GameOptionNumberOfPlayers(
            [GameOptionValueNumberOfPlayers::Players003, GameOptionValueNumberOfPlayers::Players004],
            GameOptionValueNumberOfPlayers::Players003
        );

        $autostart = new GameOptionAutostart(
            [GameOptionValueAutostart::Enabled, GameOptionValueAutostart::Disabled],
            GameOptionValueAutostart::Disabled
        );

        $forfeitAfter = new GameOptionForfeitAfter(
            [
                GameOptionValueForfeitAfter::Disabled,
                GameOptionValueForfeitAfter::Minutes10,
                GameOptionValueForfeitAfter::Hour,
            ],
            GameOptionValueForfeitAfter::Disabled
        );

        $barrelPoints = new GameOptionThousandBarrelPoints(
            [
                GameOptionValueThousandBarrelPoints::EightHundred,
                GameOptionValueThousandBarrelPoints::NineHundred,
                GameOptionValueThousandBarrelPoints::Disabled,
            ],
            GameOptionValueThousandBarrelPoints::EightHundred
        );

        $numberOfBombs = new GameOptionThousandNumberOfBombs(
            [
                GameOptionValueThousandNumberOfBombs::One,
                GameOptionValueThousandNumberOfBombs::Two,
                GameOptionValueThousandNumberOfBombs::Disabled,
            ],
            GameOptionValueThousandNumberOfBombs::One
        );

        $reDealConditions = new GameOptionThousandReDealConditions(
            [
                GameOptionValueThousandReDealConditions::Disabled,
                GameOptionValueThousandReDealConditions::FourNines,
                GameOptionValueThousandReDealConditions::TenPoints,
            ],
            GameOptionValueThousandReDealConditions::Disabled
        );

        $this->defaults = [
            $numberOfPlayers->getKey() => $numberOfPlayers,
            $autostart->getKey() => $autostart,
            $forfeitAfter->getKey() => $forfeitAfter,
            $barrelPoints->getKey() => $barrelPoints,
            $numberOfBombs->getKey() => $numberOfBombs,
            $reDealConditions->getKey() => $reDealConditions,
        ];

    }

    public function testSetDefaults(): void
    {
        $setup = new GameSetupThousand(App::make(Collection::class));
        $this->assertEquals(array_keys($this->defaults), array_keys($setup->getAllOptions()));
        $this->assertEquals(GameOptionValueAutostart::Disabled, $setup->getAutostart()->getDefaultValue());
        $this->assertEquals(GameOptionValueNumberOfPlayers::Players003, $setup->getNumberOfPlayers()->getDefaultValue());
        $this->assertCount(2, $setup->getNumberOfPlayers()->getAvailableValues());
        $this->assertCount(3, $setup->getOption('forfeitAfter')->getAvailableValues());
        $this->assertCount(6, $setup->getAllOptions());
    }
}
