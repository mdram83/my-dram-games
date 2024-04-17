<?php

namespace App\GameCore\GamePlay\Generic;

use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDeckProvider;
use App\GameCore\GamePlay\GamePlayServicesProvider;
use App\GameCore\GameRecord\GameRecordFactory;
use App\GameCore\Services\Collection\Collection;

class GamePlayServicesProviderGeneric implements GamePlayServicesProvider
{
    public function __construct(
        readonly private Collection $collectionHandler,
        readonly private PlayingCardDeckProvider $playingCardDeckProvider,
        readonly private GameRecordFactory $gameRecordFactory,
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
}
