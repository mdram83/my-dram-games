<?php

namespace GameCore\GameElements\GameDeck\PlayingCard\PhpEnum;

use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardRankPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardRank;
use PHPUnit\Framework\TestCase;

class PlayingCardRankPhpEnumTest extends TestCase
{
    public function testInterfaceInstance(): void
    {
        $rank = PlayingCardRankPhpEnum::Ace;
        $this->assertInstanceOf(PlayingCardRank::class, $rank);
    }

    public function testGetKay(): void
    {
        $rank = PlayingCardRankPhpEnum::Ace;
        $this->assertEquals($rank->value, $rank->getKey());
    }

    public function testGetName(): void
    {
        $rank = PlayingCardRankPhpEnum::Ace;
        $this->assertEquals($rank->name, $rank->getName());
    }

    public function testIsJokerTrue(): void
    {
        $rank = PlayingCardRankPhpEnum::Joker;
        $this->assertTrue($rank->isJoker());
    }

    public function testIsJokerFalse(): void
    {
        $rank = PlayingCardRankPhpEnum::Ace;
        $this->assertFalse($rank->isJoker());
    }
}
