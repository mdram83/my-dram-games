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
}
