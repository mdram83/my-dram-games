<?php

namespace Tests\Feature\GameCore\GameSetup;

use App\GameCore\GameOption\GameOption;
use App\GameCore\GameOption\GameOptionAutostart;
use App\GameCore\GameOption\GameOptionNumberOfPlayers;
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
    protected array $values;

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

        $this->values = [
            $numberOfPlayers->getKey() => GameOptionValueNumberOfPlayers::Players002,
            $autostart->getKey() => GameOptionValueAutostart::Disabled,
        ];
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

        $this->setup->configureOptions(array_merge($this->values, [$optionMock->getKey() => $valueMock]));
    }

    public function testThrowExceptionWhenUsingNotStringOptionName(): void
    {
        $this->expectException(GameSetupException::class);
        $this->expectExceptionMessage(GameSetupException::MESSAGE_OPTION_INCORRECT);

        $valueMock = $this->createMock(GameOptionValue::class);

        $this->setup->configureOptions(array_merge($this->values, [3 => $valueMock]));

    }

    public function testThrowExceptionWhenExceedingDefaults(): void
    {
        $this->expectException(GameSetupException::class);
        $this->expectExceptionMessage(GameSetupException::MESSAGE_OPTION_OUTSIDE);

        $options = array_merge($this->values, ['numberOfPlayers' => GameOptionValueNumberOfPlayers::Players009]);
        $this->setup->configureOptions($options);
    }

    public function testThrowExceptionIfClassProvidedInOptionsValues(): void
    {
        $this->expectException(GameSetupException::class);
        $this->expectExceptionMessage(GameSetupException::MESSAGE_OPTION_INCORRECT);

        $options = array_merge($this->values, ['autostart' => GameOptionValueAutostart::class]);
        $this->setup->configureOptions($options);
    }

    public function testReturnOptionWithinDefaults(): void
    {
        $options = array_merge($this->values, ['autostart' => GameOptionValueAutostart::Enabled]);
        $this->setup->configureOptions($options);
        $this->assertSame($options['autostart'], $this->setup->getAutostart()->getConfiguredValue());
    }

    public function testIsConfiguredReturnFalseIfNotAllOptionsSet(): void
    {
        $setup = new GameSetupBase(App::make(Collection::class));
        $this->assertFalse($setup->isConfigured());
    }

    public function testIsConfiguredReturnTrueAfterSettingAllOptions(): void
    {
        $this->setup->configureOptions($this->values);
        $this->assertTrue($this->setup->isConfigured());
    }
}
