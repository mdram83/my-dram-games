<?php

namespace Tests\Unit\GameCore\GameOption;

use App\GameCore\GameOption\GameOption;
use App\GameCore\GameOption\GameOptionException;
use App\GameCore\GameOption\GameOptionNumberOfPlayers;
use App\GameCore\GameOptionType\GameOptionType;
use App\GameCore\GameOptionValue\GameOptionValue;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use PHPUnit\Framework\TestCase;

class GameOptionNumberOfPlayersTest extends TestCase
{
    protected array $available = [
        GameOptionValueNumberOfPlayers::Players002,
        GameOptionValueNumberOfPlayers::Players004,
        GameOptionValueNumberOfPlayers::Players006,
    ];
    protected GameOptionValue $default = GameOptionValueNumberOfPlayers::Players002;
    protected GameOptionValue $configured = GameOptionValueNumberOfPlayers::Players006;
    protected GameOption $option;

    public function setUp(): void
    {
        parent::setUp();
        $this->option = new GameOptionNumberOfPlayers($this->available, $this->default);
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

    public function testThrowExceptionWhenAvailableValuesAreNotAutostart(): void
    {
        $this->expectException(GameOptionException::class);
        $this->expectExceptionMessage(GameOptionException::MESSAGE_INCORRECT_AVAILABLE);
        new GameOptionNumberOfPlayers(['wrong1', 'wrong2'], $this->default);
    }

    public function testThrowExceptionWhenAvailableValuesAreEmpty(): void
    {
        $this->expectException(GameOptionException::class);
        $this->expectExceptionMessage(GameOptionException::MESSAGE_INCORRECT_AVAILABLE);
        new GameOptionNumberOfPlayers([], $this->default);
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
