<?php

namespace App\GameCore\GameElements\GameDeck\PlayingCard;

interface PlayingCardSuit
{
    public function getKey(): string;
    public function getName(): string;
    public function getColor(): PlayingCardColor;
    public function getSymbol(): string;
}
