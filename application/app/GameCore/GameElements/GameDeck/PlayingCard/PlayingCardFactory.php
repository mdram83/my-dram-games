<?php

namespace App\GameCore\GameElements\GameDeck\PlayingCard;

interface PlayingCardFactory
{
    public function create(string $key): PlayingCard;
}
