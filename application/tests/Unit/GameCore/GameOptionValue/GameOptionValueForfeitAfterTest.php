<?php

namespace Tests\Unit\GameCore\GameOptionValue;

use App\GameCore\GameOptionValue\GameOptionValue;
use App\GameCore\GameOptionValue\GameOptionValueForfeitAfter;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class GameOptionValueForfeitAfterTest extends TestCase
{
    public function testInstanceOfGameOptions(): void
    {
        $reflection = new ReflectionClass(GameOptionValueForfeitAfter::class);
        $this->assertTrue($reflection->implementsInterface(GameOptionValue::class));
    }

    public function testGetValue(): void
    {
        $disabled = GameOptionValueForfeitAfter::Disabled;
        $minute1 = GameOptionValueForfeitAfter::Minute;
        $minute5 = GameOptionValueForfeitAfter::Minutes5;
        $minute10 = GameOptionValueForfeitAfter::Minutes10;
        $hour1 = GameOptionValueForfeitAfter::Hour;
        $day1 = GameOptionValueForfeitAfter::Day;

        $this->assertEquals($disabled->value, $disabled->getValue());
        $this->assertEquals($minute1->value, $minute1->getValue());
        $this->assertEquals($minute5->value, $minute5->getValue());
        $this->assertEquals($minute10->value, $minute10->getValue());
        $this->assertEquals($hour1->value, $hour1->getValue());
        $this->assertEquals($day1->value, $day1->getValue());
    }

    public function testGetLabel(): void
    {
        $disabled = GameOptionValueForfeitAfter::Disabled;
        $minute1 = GameOptionValueForfeitAfter::Minute;
        $minute5 = GameOptionValueForfeitAfter::Minutes5;
        $minute10 = GameOptionValueForfeitAfter::Minutes10;
        $hour1 = GameOptionValueForfeitAfter::Hour;
        $day1 = GameOptionValueForfeitAfter::Day;

        $this->assertEquals('Disabled', $disabled->getLabel());
        $this->assertEquals('1 Minute', $minute1->getLabel());
        $this->assertEquals('5 Minutes', $minute5->getLabel());
        $this->assertEquals('10 Minutes', $minute10->getLabel());
        $this->assertEquals('1 Hour', $hour1->getLabel());
        $this->assertEquals('1 Day', $day1->getLabel());
    }
}
