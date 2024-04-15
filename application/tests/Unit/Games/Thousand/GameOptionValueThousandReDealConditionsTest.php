<?php

namespace Games\Thousand;

use App\GameCore\GameOptionValue\GameOptionValue;
use App\Games\Thousand\GameOptionValueThousandReDealConditions;
use PHPUnit\Framework\TestCase;

class GameOptionValueThousandReDealConditionsTest extends TestCase
{
    public function testInstanceOfGameOptions(): void
    {
        $reflection = new \ReflectionClass(GameOptionValueThousandReDealConditions::class);
        $this->assertTrue($reflection->implementsInterface(GameOptionValue::class));
    }

    public function testCases(): void
    {
        $cases = GameOptionValueThousandReDealConditions::cases();
        $expected = ['Disabled', 'Four Nines', 'Ten Points', 'Eighteen Points'];

        $this->assertCount(count($expected), $cases);
        $this->assertEquals($expected, array_map(fn($case) => $case->value, $cases));
    }

    public function testGetValue(): void
    {
        $disabled = GameOptionValueThousandReDealConditions::Disabled;
        $nines = GameOptionValueThousandReDealConditions::FourNines;
        $tenPoints = GameOptionValueThousandReDealConditions::TenPoints;
        $eighteenPoints = GameOptionValueThousandReDealConditions::EighteenPoints;

        $this->assertEquals($disabled->value, $disabled->getValue());
        $this->assertEquals($nines->value, $nines->getValue());
        $this->assertEquals($tenPoints->value, $tenPoints->getValue());
        $this->assertEquals($eighteenPoints->value, $eighteenPoints->getValue());
    }

    public function testGetLabel(): void
    {
        $disabled = GameOptionValueThousandReDealConditions::Disabled;
        $nines = GameOptionValueThousandReDealConditions::FourNines;
        $tenPoints = GameOptionValueThousandReDealConditions::TenPoints;
        $eighteenPoints = GameOptionValueThousandReDealConditions::EighteenPoints;

        $this->assertEquals('Disabled', $disabled->getLabel());
        $this->assertEquals('Four Nines', $nines->getLabel());
        $this->assertEquals('Ten Points', $tenPoints->getLabel());
        $this->assertEquals('Eighteen Points', $eighteenPoints->getLabel());
    }
}
