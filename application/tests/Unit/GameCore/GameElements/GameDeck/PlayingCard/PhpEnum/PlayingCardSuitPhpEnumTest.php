<?php

namespace GameCore\GameElements\GameDeck\PlayingCard\PhpEnum;

use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardColorPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardSuitPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardSuit;
use PHPUnit\Framework\TestCase;

class PlayingCardSuitPhpEnumTest extends TestCase
{
    public function testInterfaceInstance(): void
    {
        $suit = PlayingCardSuitPhpEnum::Hearts;
        $this->assertInstanceOf(PlayingCardSuit::class, $suit);
    }

    public function testGetKey(): void
    {
        $suit = PlayingCardSuitPhpEnum::Hearts;
        $this->assertEquals($suit->value, $suit->getKey());
    }

    public function testGetName(): void
    {
        $suit = PlayingCardSuitPhpEnum::Hearts;
        $this->assertEquals($suit->name, $suit->getName());
    }

    public function testGetColor(): void
    {
        $suit = PlayingCardSuitPhpEnum::Hearts;
        $this->assertEquals(PlayingCardColorPhpEnum::Red, $suit->getColor());
    }

    public function testGetSymbol(): void
    {
        $suit = PlayingCardSuitPhpEnum::Hearts;
        $this->assertEquals('U+2665', $suit->getSymbol());
    }
}
