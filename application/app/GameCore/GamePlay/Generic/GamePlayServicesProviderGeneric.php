<?php

namespace App\GameCore\GamePlay\Generic;

use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDealer;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDeckProvider;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardSuitRepository;
use App\GameCore\GamePlay\GamePlayServicesProvider;
use App\GameCore\GameRecord\GameRecordFactory;
use App\GameCore\Services\Collection\Collection;
use MyDramGames\Utils\Player\PlayerCollection;

class GamePlayServicesProviderGeneric implements GamePlayServicesProvider
{
    public function __construct(
        readonly private Collection $collectionHandler,
        readonly private GameRecordFactory $gameRecordFactory,
        readonly private PlayingCardDeckProvider $playingCardDeckProvider,
        readonly private PlayingCardSuitRepository $playingCardSuitRepository,
        readonly private PlayingCardDealer $playingCardDealer,
        readonly private PlayerCollection $playerCollection,
    )
    {

    }

    public function getCollectionHandler(): Collection
    {
        return clone $this->collectionHandler;
    }

    public function getPlayingCardDeckProvider(): PlayingCardDeckProvider
    {
        return $this->playingCardDeckProvider;
    }

    public function getGameRecordFactory(): GameRecordFactory
    {
        return $this->gameRecordFactory;
    }

    public function getPlayingCardSuitRepository(): PlayingCardSuitRepository
    {
        return $this->playingCardSuitRepository;
    }

    public function getPlayingCardDealer(): PlayingCardDealer
    {
        return $this->playingCardDealer;
    }

    public function getPlayerCollection(): PlayerCollection
    {
        return $this->playerCollection->clone();
    }
}
