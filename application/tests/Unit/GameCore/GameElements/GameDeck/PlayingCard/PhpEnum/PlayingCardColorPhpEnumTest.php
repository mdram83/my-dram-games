<?php

namespace Tests\Unit\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum;

use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardColorPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardColor;
use PHPUnit\Framework\TestCase;

class PlayingCardColorPhpEnumTest extends TestCase
{
    public function testInterfaceInstance(): void
    {
        $this->assertInstanceOf(PlayingCardColor::class, PlayingCardColorPhpEnum::Red);
    }

    public function testGetName(): void
    {
        $color = PlayingCardColorPhpEnum::Red;
        $this->assertEquals($color->name, $color->getName());
    }

    public function testDefinition(): void
    {
        $colors = array_map(fn($color) => $color->getName(), PlayingCardColorPhpEnum::cases());
        $this->assertEquals(['Red', 'Black'], $colors);
    }
}
