<?php

namespace App\GameCore\GameElements\GameDeck\PlayingCard;

interface PlayingCard
{
    public function getKey(): string;
    public function getRank(): PlayingCardRank;
    public function getSuit(): ?PlayingCardSuit;
    public function getColor(): PlayingCardColor;
}
