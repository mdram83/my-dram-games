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

    public function testGetValue(): void
    {
        $disabled = GameOptionValueThousandNumberOfBombs::Disabled;
        $one = GameOptionValueThousandNumberOfBombs::One;
        $two = GameOptionValueThousandNumberOfBombs::Two;

        $this->assertEquals($disabled->value, $disabled->getValue());
        $this->assertEquals($one->value, $one->getValue());
        $this->assertEquals($two->value, $two->getValue());
    }

    public function testGetLabel(): void
    {
        $disabled = GameOptionValueThousandNumberOfBombs::Disabled;
        $one = GameOptionValueThousandNumberOfBombs::One;
        $two = GameOptionValueThousandNumberOfBombs::Two;

        $this->assertEquals('Disabled', $disabled->getLabel());
        $this->assertEquals('One Bomb', $one->getLabel());
        $this->assertEquals('Two Bombs', $two->getLabel());
    }
}
