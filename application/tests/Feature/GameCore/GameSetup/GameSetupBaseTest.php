<?php

namespace Tests\Feature\GameCore\GameSetup;

use App\GameCore\GameOption\GameOption;
use App\GameCore\GameOption\GameOptionAutostart;
use App\GameCore\GameOption\GameOptionNumberOfPlayers;
use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\GameOptionValue\GameOptionValue;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\GameSetup\GameSetup;
use App\GameCore\GameSetup\GameSetupBase;
use App\GameCore\GameSetup\GameSetupException;
use App\GameCore\Services\Collection\Collection;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class GameSetupBaseTest extends TestCase
{
    protected GameSetupBase $setup;
    protected CollectionGameOptionValueInput $options;

    public function setUp(): void
    {
        parent::setUp();

        $this->setup = new GameSetupBase(App::make(Collection::class));

        $numberOfPlayers = new GameOptionNumberOfPlayers(
            [GameOptionValueNumberOfPlayers::Players002],
            GameOptionValueNumberOfPlayers::Players002
        );

        $autostart = new GameOptionAutostart(
            [GameOptionValueAutostart::Enabled, GameOptionValueAutostart::Disabled],
            GameOptionValueAutostart::Enabled
        );

        $this->options = new CollectionGameOptionValueInput(
            App::make(Collection::class),
            [
                $numberOfPlayers->getKey() => GameOptionValueNumberOfPlayers::Players002,
                $autostart->getKey() => GameOptionValueAutostart::Disabled,
            ]
        );
    }

    public function testInstanceOfGameSetup(): void
    {
        $this->assertInstanceOf(GameSetup::class, $this->setup);
    }

    public function testGetAutostartTwoWays(): void
    {
        $this->assertInstanceOf(GameOptionAutostart::class, $this->setup->getAutostart());
        $this->assertInstanceOf(GameOptionAutostart::class, $this->setup->getOption('autostart'));
    }

    public function testThrowExceptionWhenGettingMissingOption(): void
    {
        $this->expectException(GameSetupException::class);
        $this->expectExceptionMessage(GameSetupException::MESSAGE_OPTION_NOT_SET);

        $this->setup->getOption('definitely-missing-123-option');
    }

    public function testThrowExceptionWhenSetOptionKeyNotInDefaults(): void
    {
        $this->expectException(GameSetupException::class);
        $this->expectExceptionMessage(GameSetupException::MESSAGE_OPTION_OUTSIDE);

        $optionMock = $this->createMock(GameOption::class);
        $optionMock->method('getKey')->willReturn('test-option-123');
        $valueMock = $this->createMock(GameOptionValue::class);

        $this->setup->configureOptions($this->options->add($valueMock, $optionMock->getKey()));
    }

    public function testThrowExceptionWhenExceedingDefaults(): void
    {
        $this->expectException(GameSetupException::class);
        $this->expectExceptionMessage(GameSetupException::MESSAGE_OPTION_OUTSIDE);

        $options = $this->options->reset([
            'numberOfPlayers' => GameOptionValueNumberOfPlayers::Players009,
            'autostart' => GameOptionValueAutostart::Disabled,
        ]);
        $this->setup->configureOptions($options);
    }

    public function testReturnOptionConfiguredWithinDefaults(): void
    {
        $newValues = [
            'numberOfPlayers' => GameOptionValueNumberOfPlayers::Players002,
            'autostart' => GameOptionValueAutostart::Enabled,
        ];
        $this->options->reset($newValues)->toArray();
        $this->setup->configureOptions($this->options);
        $this->assertSame($this->options->getOne('autostart'), $this->setup->getAutostart()->getConfiguredValue());
    }

    public function testIsConfiguredReturnFalseIfNotAllOptionsSet(): void
    {
        $setup = new GameSetupBase(App::make(Collection::class));
        $this->assertFalse($setup->isConfigured());
    }

    public function testIsConfiguredReturnTrueAfterSettingAllOptions(): void
    {
        $this->setup->configureOptions($this->options);
        $this->assertTrue($this->setup->isConfigured());
    }
}
