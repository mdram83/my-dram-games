<?php

namespace App\GameCore\GameElements\GameDeck\PlayingCard;

interface PlayingCardSuitRepository
{
    public function getOne(string $key): PlayingCardSuit;
}
