<?php

namespace Tests\Unit\GameCore\GameOptionValue;

use App\GameCore\GameOptionValue\GameOptionValue;
use App\GameCore\GameOptionValue\GameOptionValueForfeitAfter;
use PHPUnit\Framework\TestCase;

class GameOptionValueForfeitAfterTest extends TestCase
{
    public function testInstanceOfGameOptions(): void
    {
        $reflection = new \ReflectionClass(GameOptionValueForfeitAfter::class);
        $this->assertTrue($reflection->implementsInterface(GameOptionValue::class));
    }
}
