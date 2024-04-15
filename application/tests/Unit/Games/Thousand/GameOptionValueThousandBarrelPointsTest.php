<?php

namespace Games\Thousand;

use App\GameCore\GameOptionValue\GameOptionValue;
use App\Games\Thousand\GameOptionValueThousandBarrelPoints;
use PHPUnit\Framework\TestCase;

class GameOptionValueThousandBarrelPointsTest extends TestCase
{
    public function testInstanceOfGameOptions(): void
    {
        $reflection = new \ReflectionClass(GameOptionValueThousandBarrelPoints::class);
        $this->assertTrue($reflection->implementsInterface(GameOptionValue::class));
    }

    public function testCases(): void
    {
        $cases = GameOptionValueThousandBarrelPoints::cases();

        $this->assertCount(4, $cases);
        $this->assertEquals([0, 800, 880, 900], array_map(fn($case) => $case->value, $cases));
    }
}
