<?php

namespace App\GameCore\GameElements\GameDeck\PlayingCard;

interface PlayingCardRankRepository
{
    public function getOne(string $key): PlayingCardRank;
}
