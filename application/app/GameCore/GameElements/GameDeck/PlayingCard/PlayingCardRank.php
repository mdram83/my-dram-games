<?php

namespace App\GameCore\GameElements\GameDeck\PlayingCard;

interface PlayingCardRank
{
    public function getKey(): string;
    public function getName(): string;
}
