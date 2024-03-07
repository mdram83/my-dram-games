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
}
