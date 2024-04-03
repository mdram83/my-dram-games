<?php

namespace Tests\Feature\Games\TicTacToe;

use App\GameCore\GameOption\GameOptionAutostart;
use App\GameCore\GameOption\GameOptionForfeitAfter;
use App\GameCore\GameOption\GameOptionNumberOfPlayers;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueForfeitAfter;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\Services\Collection\Collection;
use App\Games\TicTacToe\GameSetupTicTacToe;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class GameSetupTicTacToeTest extends TestCase
{
    protected array $defaults;

    public function setUp(): void
    {
        parent::setUp();

        $numberOfPlayers = new GameOptionNumberOfPlayers(
            [GameOptionValueNumberOfPlayers::Players002],
            GameOptionValueNumberOfPlayers::Players002
        );

        $autostart = new GameOptionAutostart(
            [GameOptionValueAutostart::Enabled, GameOptionValueAutostart::Disabled],
            GameOptionValueAutostart::Disabled
        );

        $forfeitAfter = new GameOptionForfeitAfter(
            [GameOptionValueForfeitAfter::Disabled, GameOptionValueForfeitAfter::Minute],
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
        $setup = new GameSetupTicTacToe(App::make(Collection::class));
        $this->assertEquals(array_keys($this->defaults), array_keys($setup->getAllOptions()));
        $this->assertEquals(GameOptionValueAutostart::Disabled, $setup->getAutostart()->getDefaultValue());
        $this->assertEquals(GameOptionValueNumberOfPlayers::Players002, $setup->getNumberOfPlayers()->getDefaultValue());
        $this->assertCount(1, $setup->getNumberOfPlayers()->getAvailableValues());
    }
}
