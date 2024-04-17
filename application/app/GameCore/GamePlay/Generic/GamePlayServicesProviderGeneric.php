<?php

namespace App\GameCore\GamePlay\Generic;

use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDeckProvider;
use App\GameCore\GamePlay\GamePlayServicesProvider;
use App\GameCore\Services\Collection\Collection;

class GamePlayServicesProviderGeneric implements GamePlayServicesProvider
{
    public function __construct(
        readonly private Collection $collectionHandler,
        readonly private PlayingCardDeckProvider $playingCardDeckProvider
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
}
