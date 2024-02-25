<?php

namespace Tests\Unit\GameCore\GameSetup;

use App\GameCore\GameSetup\GameSetup;
use App\GameCore\GameSetup\GameSetupBase;
use App\GameCore\GameSetup\GameSetupException;
use PHPUnit\Framework\TestCase;

class GameSetupBaseTest extends TestCase
{
    protected array $options = [
        'numberOfPlayers' => 2,
        'autostart' => true,
    ];

    public function testInstanceOfGameSetup(): void
    {
        $this->assertInstanceOf(GameSetup::class, new GameSetupBase());
    }

    public function testGetAutostartReturnArrayOfBooleanOptions(): void
    {
        $setup = new GameSetupBase();
        $expected = [false, true];

        $this->assertEquals($expected, $setup->getAutostart());
        $this->assertEquals($expected, $setup->getOption('autostart'));
    }

    public function testThrowExceptionWhenGettingMissingOption(): void
    {
        $this->expectException(GameSetupException::class);
        $this->expectExceptionMessage(GameSetupException::MESSAGE_OPTION_NOT_SET);

        $setup = new GameSetupBase();
        $setup->getOption('definitely-missing-123-option');
    }

    public function testThrowExceptionWhenOptionKeyNotInDefaults(): void
    {
        $this->expectException(GameSetupException::class);
        $this->expectExceptionMessage(GameSetupException::MESSAGE_OPTION_OUTSIDE);

        $setup = new GameSetupBase();
        $options = array_merge($this->options, ['my-option' => 'string']);
        $setup->setOptions($options);
    }

    public function testThrowExceptionWhenUsingNotStringOptionName(): void
    {
        $this->expectException(GameSetupException::class);
        $this->expectExceptionMessage(GameSetupException::MESSAGE_OPTION_INCORRECT);

        $setup = new GameSetupBase();
        $options = array_merge($this->options, ['string-value-deauflt-zero-key']);
        $setup->setOptions($options);

    }

    public function testThrowExceptionWhenExceedingDefaults(): void
    {
        $this->expectException(GameSetupException::class);
        $this->expectExceptionMessage(GameSetupException::MESSAGE_OPTION_OUTSIDE);

        $setup = new GameSetupBase();
        $options = array_merge($this->options, ['autostart' => 'exceeding default']);
        $setup->setOptions($options);
    }

    public function testThrowExceptionIfArrayProvidedInOptionsValues(): void
    {
        $this->expectException(GameSetupException::class);
        $this->expectExceptionMessage(GameSetupException::MESSAGE_OPTION_INCORRECT);
        $setup = new GameSetupBase();
        $options = array_merge($this->options, ['autostart' => [true, false]]);
        $setup->setOptions($options);
    }

    public function testReturnOptionWithinDefaults(): void
    {
        $options = array_merge($this->options, ['autostart' => true]);
        $setup = new GameSetupBase();
        $setup->setOptions($options);
        $this->assertSame([$options['autostart']], $setup->getAutostart());
    }

    public function testIsConfiguredReturnFalseIfNotAllOptionsSet(): void
    {
        $setup = new GameSetupBase();
        $this->assertFalse($setup->isConfigured());
    }

    public function testIsConfiguredReturnTrueAfterSettingAllOptions(): void
    {
        $options = array_merge($this->options, ['autostart' => true]);
        $setup = new GameSetupBase();
        $setup->setOptions($options);
        $this->assertTrue($setup->isConfigured());
    }
}
