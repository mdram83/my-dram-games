<?php

namespace GameCore\GameElements\GameDeck\PlayingCard\PhpEnum;

use App\GameCore\GameElements\GameDeck\PlayingCard\Generic\PlayingCardGeneric;
use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardColorPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardFactoryPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardRankPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardSuitPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCard;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardException;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardFactory;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class PlayingCardFactoryPhpEnumTest extends TestCase
{
    private PlayingCardFactoryPhpEnum $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = App::make(PlayingCardFactory::class);
    }
    public function testInterfaceInstance(): void
    {
        $this->assertInstanceOf(PlayingCardFactory::class, $this->factory);
    }

    public function testThrowExceptionIncorrectRank(): void
    {
        $this->expectException(PlayingCardException::class);
        $this->expectExceptionMessage(PlayingCardException::MESSAGE_MISSING_RANK);

        $key = '21' . PlayingCardGeneric::PLAYING_CARD_KEY_SEPARATOR . PlayingCardSuitPhpEnum::Hearts->getKey();
        $this->factory->create($key);
    }

    public function testThrowExceptionJokerIncorrectColor(): void
    {
        $this->expectException(PlayingCardException::class);
        $this->expectExceptionMessage(PlayingCardException::MESSAGE_MISSING_COLOR);

        $key = PlayingCardRankPhpEnum::Joker->getKey() . PlayingCardGeneric::PLAYING_CARD_KEY_SEPARATOR . 'Rainy';
        $this->factory->create($key);
    }

    public function testThrowExceptionNonJokerIncorrectSuit(): void
    {
        $this->expectException(PlayingCardException::class);
        $this->expectExceptionMessage(PlayingCardException::MESSAGE_MISSING_SUIT);

        $key = PlayingCardRankPhpEnum::Ace->getKey() . PlayingCardGeneric::PLAYING_CARD_KEY_SEPARATOR . 'HeartsAndFires';
        $this->factory->create($key);
    }

    public function testCreateJoker(): void
    {
        $key =
            PlayingCardRankPhpEnum::Joker->getKey()
            . PlayingCardGeneric::PLAYING_CARD_KEY_SEPARATOR
            . PlayingCardColorPhpEnum::Red->getName();
        $card = $this->factory->create($key);

        $this->assertInstanceOf(PlayingCard::class, $card);
        $this->assertEquals($key, $card->getKey());
    }

    public function testCreateNonJoker(): void
    {
        $key =
            PlayingCardRankPhpEnum::Ace->getKey()
            . PlayingCardGeneric::PLAYING_CARD_KEY_SEPARATOR
            . PlayingCardSuitPhpEnum::Hearts->getKey();
        $card = $this->factory->create($key);

        $this->assertInstanceOf(PlayingCard::class, $card);
        $this->assertEquals($key, $card->getKey());
    }


    // non joker suit created
}
