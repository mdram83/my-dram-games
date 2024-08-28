<?php

namespace Tests\Feature\GameCore\GamePlay\Generic;

use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDealer;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDeckProvider;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardSuitRepository;
use App\GameCore\GamePlay\GamePlayServicesProvider;
use App\GameCore\GamePlay\Generic\GamePlayServicesProviderGeneric;
use App\GameCore\GameRecord\GameRecordFactory;
use App\GameCore\Services\Collection\Collection;
use Illuminate\Support\Facades\App;
use MyDramGames\Utils\Player\PlayerCollection;
use Tests\TestCase;

class GamePlayServicesProviderGenericTest extends TestCase
{
    private GamePlayServicesProviderGeneric $provider;

    public function setUp(): void
    {
        parent::setUp();
        $this->provider = App::make(GamePlayServicesProviderGeneric::class);
    }

    public function testInterface(): void
    {
        $this->assertInstanceOf(GamePlayServicesProvider::class, $this->provider);
    }

    public function testGetCollectionHandler(): void
    {
        $this->assertInstanceOf(Collection::class, $this->provider->getCollectionHandler());
    }

    public function testGetPlayingCardDeckProvider(): void
    {
        $this->assertInstanceOf(PlayingCardDeckProvider::class, $this->provider->getPlayingCardDeckProvider());
    }

    public function testGetGameRecordFactory(): void
    {
        $this->assertInstanceOf(GameRecordFactory::class, $this->provider->getGameRecordFactory());
    }

    public function testGetPlayingCardSuitRepository(): void
    {
        $this->assertInstanceOf(PlayingCardSuitRepository::class, $this->provider->getPlayingCardSuitRepository());
    }

    public function testGetPlayingCardDealer(): void
    {
        $this->assertInstanceOf(PlayingCardDealer::class, $this->provider->getPlayingCardDealer());
    }

    public function testGetPlayerCollection(): void
    {
        $this->assertInstanceOf(PlayerCollection::class, $this->provider->getPlayerCollection());
    }
}
