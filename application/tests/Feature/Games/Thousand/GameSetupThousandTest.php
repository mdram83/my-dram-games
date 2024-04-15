<?php

namespace Games\Thousand;

use App\GameCore\GameOption\GameOptionAutostart;
use App\GameCore\GameOption\GameOptionForfeitAfter;
use App\GameCore\GameOption\GameOptionNumberOfPlayers;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueForfeitAfter;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\Services\Collection\Collection;
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
            [GameOptionValueForfeitAfter::Disabled, GameOptionValueForfeitAfter::Minutes5],
            GameOptionValueForfeitAfter::Disabled
        );

        $this->defaults = [
            $numberOfPlayers->getKey() => $numberOfPlayers,
            $autostart->getKey() => $autostart,
            $forfeitAfter->getKey() => $forfeitAfter,
        ];

    }

    public function testSetDefaults(): void
    {
        $setup = new GameSetupThousand(App::make(Collection::class));
        $this->assertEquals(array_keys($this->defaults), array_keys($setup->getAllOptions()));
        $this->assertEquals(GameOptionValueAutostart::Disabled, $setup->getAutostart()->getDefaultValue());
        $this->assertEquals(GameOptionValueNumberOfPlayers::Players003, $setup->getNumberOfPlayers()->getDefaultValue());
        $this->assertCount(2, $setup->getNumberOfPlayers()->getAvailableValues());
        $this->assertCount(2, $setup->getOption('forfeitAfter')->getAvailableValues());
    }
}
