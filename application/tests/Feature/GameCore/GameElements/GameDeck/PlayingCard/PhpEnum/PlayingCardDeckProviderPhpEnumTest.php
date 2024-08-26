<?php

namespace Tests\Feature\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum;

use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardDeckProviderPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardRankPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardSuitPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDeckProvider;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class PlayingCardDeckProviderPhpEnumTest extends TestCase
{
    private PlayingCardDeckProviderPhpEnum $provider;

    public function setUp(): void
    {
        parent::setUp();
        $this->provider = App::make(PlayingCardDeckProvider::class);
    }
    public function testInterfaceImplementation(): void
    {
        $this->assertInstanceOf(PlayingCardDeckProvider::class, $this->provider);
    }

    public function testGetDeckSchnapsen(): void
    {
        $deck = $this->provider->getDeckSchnapsen();

        $this->assertEquals(24, $deck->count());

        $this->assertEquals(6, $deck->filter(fn($item, $key) => $item->getSuit()->getKey() === PlayingCardSuitPhpEnum::Hearts->getKey())->count());
        $this->assertEquals(6, $deck->filter(fn($item, $key) => $item->getSuit()->getKey() === PlayingCardSuitPhpEnum::Diamonds->getKey())->count());
        $this->assertEquals(6, $deck->filter(fn($item, $key) => $item->getSuit()->getKey() === PlayingCardSuitPhpEnum::Spades->getKey())->count());
        $this->assertEquals(6, $deck->filter(fn($item, $key) => $item->getSuit()->getKey() === PlayingCardSuitPhpEnum::Clubs->getKey())->count());

        $this->assertEquals(4, $deck->filter(fn($item, $key) => $item->getRank()->getKey() === PlayingCardRankPhpEnum::Nine->getKey())->count());
        $this->assertEquals(4, $deck->filter(fn($item, $key) => $item->getRank()->getKey() === PlayingCardRankPhpEnum::Ten->getKey())->count());
        $this->assertEquals(4, $deck->filter(fn($item, $key) => $item->getRank()->getKey() === PlayingCardRankPhpEnum::Jack->getKey())->count());
        $this->assertEquals(4, $deck->filter(fn($item, $key) => $item->getRank()->getKey() === PlayingCardRankPhpEnum::Queen->getKey())->count());
        $this->assertEquals(4, $deck->filter(fn($item, $key) => $item->getRank()->getKey() === PlayingCardRankPhpEnum::King->getKey())->count());
        $this->assertEquals(4, $deck->filter(fn($item, $key) => $item->getRank()->getKey() === PlayingCardRankPhpEnum::Ace->getKey())->count());
    }
}
