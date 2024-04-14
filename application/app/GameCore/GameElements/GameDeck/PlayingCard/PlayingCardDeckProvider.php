<?php

namespace App\GameCore\GameElements\GameDeck\PlayingCard;

interface PlayingCardDeckProvider
{
    public function getDeckSchnapsen(): CollectionPlayingCard;
}
