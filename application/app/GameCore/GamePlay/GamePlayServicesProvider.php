<?php

namespace App\GameCore\GamePlay;

use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDealer;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDeckProvider;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardSuitRepository;
use App\GameCore\GameRecord\GameRecordFactory;
use App\GameCore\Services\Collection\Collection;

interface GamePlayServicesProvider
{
    public function getCollectionHandler(): Collection;
    public function getGameRecordFactory(): GameRecordFactory;
    public function getPlayingCardDeckProvider(): PlayingCardDeckProvider;
    public function getPlayingCardSuitRepository(): PlayingCardSuitRepository;
    public function getPlayingCardDealer(): PlayingCardDealer;
}
