<?php

namespace Tests\Unit\GameCore\GameOption;

use App\GameCore\GameOption\GameOption;
use App\GameCore\GameOption\GameOptionAutostart;
use App\GameCore\GameOption\GameOptionException;
use App\GameCore\GameOptionType\GameOptionType;
use App\GameCore\GameOptionValue\GameOptionValue;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use PHPUnit\Framework\TestCase;

class GameOptionAutostartTest extends TestCase
{
    protected array $available = [GameOptionValueAutostart::Enabled, GameOptionValueAutostart::Disabled];
    protected GameOptionValue $default = GameOptionValueAutostart::Disabled;
    protected GameOptionValue $configured = GameOptionValueAutostart::Enabled;
    protected GameOption $option;

    public function setUp(): void
    {
        parent::setUp();
        $this->option = new GameOptionAutostart($this->available, $this->default);
    }

    public function testInstanceOfGameOption(): void
    {
        $this->assertInstanceOf(GameOption::class, $this->option);
    }

    public function testGetKeyReturnString(): void
    {
        $this->assertNotNull($this->option->getKey());
        $this->assertIsString($this->option->getKey());
    }

    public function testGetNameReturnString(): void
    {
        $this->assertNotNull($this->option->getName());
        $this->assertIsString($this->option->getName());
    }

    public function testGetDescriptionReturnString(): void
    {
        $this->assertNotNull($this->option->getDescription());
        $this->assertIsString($this->option->getDescription());
    }

    public function testGetTypeReturnGameOptionType(): void
    {
        $this->assertInstanceOf(GameOptionType::class, $this->option->getType());
    }

    public function testGetOptionValueClass(): void
    {
        $this->assertEquals(GameOptionValueAutostart::class, $this->option->getOptionValueClass());
    }

    public function testThrowExceptionWhenAvailableValuesAreNotAutostart(): void
    {
        $this->expectException(GameOptionException::class);
        $this->expectExceptionMessage(GameOptionException::MESSAGE_INCORRECT_AVAILABLE);
        new GameOptionAutostart(['wrong1', 'wrong2'], $this->default);
    }

    public function testThrowExceptionWhenAvailableValuesAreEmpty(): void
    {
        $this->expectException(GameOptionException::class);
        $this->expectExceptionMessage(GameOptionException::MESSAGE_INCORRECT_AVAILABLE);
        new GameOptionAutostart([], $this->default);
    }

    public function testGetDefaultValue(): void
    {
        $this->assertEquals(GameOptionValueAutostart::Disabled, $this->option->getDefaultValue());
    }

    public function testGetAvailableValues(): void
    {
        $this->assertEquals($this->available, $this->option->getAvailableValues());
    }

    public function testGetConfiguredValueThrowExceptionIfNotSet(): void
    {
        $this->expectException(GameOptionException::class);
        $this->expectExceptionMessage(GameOptionException::MESSAGE_NOT_CONFIGURED);
        $this->option->getConfiguredValue();
    }

    public function testSetConfiguredValueThrowExceptionIfAlreadySet(): void
    {
        $this->expectException(GameOptionException::class);
        $this->expectExceptionMessage(GameOptionException::MESSAGE_ALREADY_CONFIGURED);
        $this->option->setConfiguredValue($this->configured);
        $this->option->setConfiguredValue($this->configured);
    }

    public function testSetAndGetConfiguredValue(): void
    {
        $this->option->setConfiguredValue($this->configured);
        $this->assertSame($this->configured, $this->option->getConfiguredValue());
    }

    public function testIsConfigured(): void
    {
        $this->assertFalse($this->option->isConfigured());
        $this->option->setConfiguredValue($this->configured);
        $this->assertTrue($this->option->isConfigured());
    }
}
