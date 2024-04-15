<?php

namespace Games\Thousand;

use App\GameCore\GameOptionValue\GameOptionValue;
use App\Games\Thousand\GameOptionValueThousandNumberOfBombs;
use PHPUnit\Framework\TestCase;

class GameOptionValueThousandNumberOfBombsTest extends TestCase
{
    public function testInstanceOfGameOptions(): void
    {
        $reflection = new \ReflectionClass(GameOptionValueThousandNumberOfBombs::class);
        $this->assertTrue($reflection->implementsInterface(GameOptionValue::class));
    }

    public function testCases(): void
    {
        $cases = GameOptionValueThousandNumberOfBombs::cases();

        $this->assertCount(3, $cases);
        $this->assertEquals([0, 1, 2], array_map(fn($case) => $case->value, $cases));
    }
}
