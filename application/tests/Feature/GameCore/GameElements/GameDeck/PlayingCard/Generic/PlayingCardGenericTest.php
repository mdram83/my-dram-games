<?php

namespace Tests\Feature\GameCore\GameElements\GameDeck\PlayingCard\Generic;

use App\GameCore\GameElements\GameDeck\PlayingCard\Generic\PlayingCardGeneric;
use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardColorPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardRankPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardSuitPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCard;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardColor;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardException;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardRank;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardSuit;
use Tests\TestCase;

class PlayingCardGenericTest extends TestCase
{
    private PlayingCardRank $aceRank;
    private PlayingCardRank $jokerRank;
    private PlayingCardSuit $suit;
    private PlayingCardColor $color;

    public function setUp(): void
    {
        parent::setUp();
        $this->aceRank = PlayingCardRankPhpEnum::Ace;
        $this->jokerRank = PlayingCardRankPhpEnum::Joker;
        $this->suit = PlayingCardSuitPhpEnum::Hearts;
        $this->color = PlayingCardColorPhpEnum::Red;
    }
    public function testNewThrowExceptionJokerWithSuit(): void
    {
        $this->expectException(PlayingCardException::class);
        $this->expectExceptionMessage(PlayingCardException::MESSAGE_INCORRECT_PARAMS);

        new PlayingCardGeneric($this->jokerRank, $this->suit, $this->color);
    }

    public function testNewThrowExceptionJokerWithoutColor(): void
    {
        $this->expectException(PlayingCardException::class);
        $this->expectExceptionMessage(PlayingCardException::MESSAGE_INCORRECT_PARAMS);

        new PlayingCardGeneric($this->jokerRank, null, null);
    }

    public function testNewThrowExceptionNonJokerWithoutSuit(): void
    {
        $this->expectException(PlayingCardException::class);
        $this->expectExceptionMessage(PlayingCardException::MESSAGE_INCORRECT_PARAMS);

        new PlayingCardGeneric($this->aceRank, null, null);
    }

    public function testNewThrowExceptionNonJokerWithColor(): void
    {
        $this->expectException(PlayingCardException::class);
        $this->expectExceptionMessage(PlayingCardException::MESSAGE_INCORRECT_PARAMS);

        new PlayingCardGeneric($this->aceRank, $this->suit, $this->color);
    }

    public function testJokerCreated(): void
    {
        $card = new PlayingCardGeneric($this->jokerRank, null, $this->color);
        $this->assertInstanceOf(PlayingCard::class, $card);
    }

    public function testNonJokerCreated(): void
    {
        $card = new PlayingCardGeneric($this->aceRank, $this->suit, null);
        $this->assertInstanceOf(PlayingCard::class, $card);
    }

    public function testGetKeyJoker(): void
    {
        $card = new PlayingCardGeneric($this->jokerRank, null, $this->color);
        $jokerKey = $this->jokerRank->getKey() . PlayingCardGeneric::PLAYING_CARD_KEY_SEPARATOR . $this->color->getName();

        $this->assertEquals($jokerKey, $card->getKey());
    }

    public function testGetKeyNonJoker(): void
    {
        $card = new PlayingCardGeneric($this->aceRank, $this->suit);
        $nonJokerKey = $this->aceRank->getKey() . PlayingCardGeneric::PLAYING_CARD_KEY_SEPARATOR . $this->suit->getKey();

        $this->assertEquals($nonJokerKey, $card->getKey());
    }

    public function testGetRank(): void
    {
        $card = new PlayingCardGeneric($this->aceRank, $this->suit);
        $this->assertEquals($this->aceRank, $card->getRank());
    }

    public function testGetSuitJokerNull(): void
    {
        $card = new PlayingCardGeneric($this->jokerRank, null, $this->color);
        $this->assertNull($card->getSuit());
    }

    public function testGetSuitNonJoker(): void
    {
        $card = new PlayingCardGeneric($this->aceRank, $this->suit);
        $this->assertEquals($this->suit, $card->getSuit());
    }

    public function testGetColorJoker(): void
    {
        $card = new PlayingCardGeneric($this->jokerRank, null, $this->color);
        $this->assertEquals($this->color, $card->getColor());
    }

    public function testColorNonJoker(): void
    {
        $card = new PlayingCardGeneric($this->aceRank, $this->suit);
        $this->assertEquals($this->suit->getColor(), $card->getColor());
    }
}
