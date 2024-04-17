<?php

namespace App\GameCore\GamePlay;

use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDeckProvider;
use App\GameCore\GameRecord\GameRecordFactory;
use App\GameCore\Services\Collection\Collection;

interface GamePlayServicesProvider
{
    public function getCollectionHandler(): Collection;
    public function getPlayingCardDeckProvider(): PlayingCardDeckProvider;
    public function getGameRecordFactory(): GameRecordFactory;
}
