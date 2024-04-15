<?php

namespace Tests\Unit\GameCore\GameOptionValue;

use App\GameCore\GameOptionValue\GameOptionValue;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use PHPUnit\Framework\TestCase;

class GameOptionValueAutostartTest extends TestCase
{
    public function testInstanceOfGameOptions(): void
    {
        $reflection = new \ReflectionClass(GameOptionValueAutostart::class);
        $this->assertTrue($reflection->implementsInterface(GameOptionValue::class));
    }

    public function testGetValue(): void
    {
        $enabled = GameOptionValueAutostart::Enabled;
        $disabled = GameOptionValueAutostart::Disabled;

        $this->assertEquals($enabled->value, $enabled->getValue());
        $this->assertEquals($disabled->value, $disabled->getValue());
    }

    public function testGetLabel(): void
    {
        $enabled = GameOptionValueAutostart::Enabled;
        $disabled = GameOptionValueAutostart::Disabled;

        $this->assertEquals('Enabled', $enabled->getLabel());
        $this->assertEquals('Disabled', $disabled->getLabel());
    }
}
