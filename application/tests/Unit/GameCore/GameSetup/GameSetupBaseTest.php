<?php

namespace Tests\Unit\GameCore\GameSetup;

use App\GameCore\GameSetup\GameSetup;
use App\GameCore\GameSetup\GameSetupBase;
use App\GameCore\GameSetup\GameSetupException;
use PHPUnit\Framework\TestCase;

class GameSetupBaseTest extends TestCase
{
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

    public function testThrowExceptionWhenGettingUnsetNumberOfPlayersThroghDedicatedMethod(): void
    {
        $this->expectException(GameSetupException::class);
        $this->expectExceptionMessage(GameSetupException::MESSAGE_OPTION_NOT_SET);

        $setup = new GameSetupBase();
        $setup->getNumberOfPlayers();
    }

    public function testThrowExceptionWhenGettingUnsetNumberOfPlayersThroghGenericMethod(): void
    {
        $this->expectException(GameSetupException::class);
        $this->expectExceptionMessage(GameSetupException::MESSAGE_OPTION_NOT_SET);

        $setup = new GameSetupBase();
        $setup->getOption('numberOfPlayers');
    }

    public function testThrowExceptionWhenGettingMissingOption(): void
    {
        $this->expectException(GameSetupException::class);
        $this->expectExceptionMessage(GameSetupException::MESSAGE_OPTION_NOT_SET);

        $setup = new GameSetupBase();
        $setup->getOption('definitely-missing-123-option');
    }

    public function testThrowExceptionWhenAnyOptionIsNotAnArray(): void
    {
        $this->expectException(GameSetupException::class);
        $this->expectExceptionMessage(GameSetupException::MESSAGE_OPTION_INCORRECT);

        new GameSetupBase(['my-option' => 'string']);

    }

    public function testThrowExceptionWhenUsingNotStringOptionName(): void
    {
        $this->expectException(GameSetupException::class);
        $this->expectExceptionMessage(GameSetupException::MESSAGE_OPTION_INCORRECT);

        new GameSetupBase(['string-value-deauflt-zero-key']);

    }

    public function testThrowExceptionWhenExceedingDefaults(): void
    {
        $this->expectException(GameSetupException::class);
        $this->expectExceptionMessage(GameSetupException::MESSAGE_OPTION_OUTSIDE);

        new GameSetupBase(['autostart' => ['exceeding default']]);
    }

    public function testReturnOptionWithinDefaults(): void
    {
        $options = ['autostart' => [true]];
        $setup = new GameSetupBase($options);
        $this->assertSame($options['autostart'], $setup->getAutostart());
    }

    public function testReturnOptionNotRestrictedByDefaults(): void
    {
        $optionName = 'test-option';
        $options = [$optionName => [1, 2, 3]];
        $setup = new GameSetupBase($options);
        $this->assertSame($options[$optionName], $setup->getOption($optionName));
    }

    public function testThrowExceptionIfDefaultOptionsWereNotOverwrittenAndTryingToGetAll(): void
    {
        $this->expectException(GameSetupException::class);
        $this->expectExceptionMessage(GameSetupException::MESSAGE_OPTION_NOT_SET);
        $setup = new GameSetupBase();
        $setup->getAllOptions();
    }
}
