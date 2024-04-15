<?php

namespace Games\Thousand;

use App\GameCore\GameOptionValue\GameOptionValue;
use App\Games\Thousand\GameOptionValueThousandBarrelPoints;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class GameOptionValueThousandBarrelPointsTest extends TestCase
{
    public function testInstanceOfGameOptions(): void
    {
        $reflection = new ReflectionClass(GameOptionValueThousandBarrelPoints::class);
        $this->assertTrue($reflection->implementsInterface(GameOptionValue::class));
    }

    public function testCases(): void
    {
        $cases = GameOptionValueThousandBarrelPoints::cases();

        $this->assertCount(4, $cases);
        $this->assertEquals([0, 800, 880, 900], array_map(fn($case) => $case->value, $cases));
    }

    public function testGetValue(): void
    {
        $disabled = GameOptionValueThousandBarrelPoints::Disabled;
        $eightHundred = GameOptionValueThousandBarrelPoints::EightHundred;
        $eightHundredEighty = GameOptionValueThousandBarrelPoints::EightHundredEighty;
        $nineHundred = GameOptionValueThousandBarrelPoints::NineHundred;

        $this->assertEquals($disabled->value, $disabled->getValue());
        $this->assertEquals($eightHundred->value, $eightHundred->getValue());
        $this->assertEquals($eightHundredEighty->value, $eightHundredEighty->getValue());
        $this->assertEquals($nineHundred->value, $nineHundred->getValue());
    }

    public function testGetLabel(): void
    {
        $disabled = GameOptionValueThousandBarrelPoints::Disabled;
        $eightHundred = GameOptionValueThousandBarrelPoints::EightHundred;
        $eightHundredEighty = GameOptionValueThousandBarrelPoints::EightHundredEighty;
        $nineHundred = GameOptionValueThousandBarrelPoints::NineHundred;

        $this->assertEquals('Disabled', $disabled->getLabel());
        $this->assertEquals('Eight Hundred', $eightHundred->getLabel());
        $this->assertEquals('Eight Hundred Eighty', $eightHundredEighty->getLabel());
        $this->assertEquals('Nine Hundred', $nineHundred->getLabel());
    }
}
