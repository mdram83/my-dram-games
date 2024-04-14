<?php

namespace App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum;

use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardColor;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardSuit;

enum PlayingCardSuitPhpEnum: string implements PlayingCardSuit
{
    case Hearts = 'H';
    case Diamonds = 'D';
    case Clubs = 'C';
    case Spades = 'S';

    public function getKey(): string
    {
        return $this->value;
    }

    public function getName(): string
    {
        return $this->name;
    }
    public function getColor(): PlayingCardColor
    {
        return match($this) {
            PlayingCardSuitPhpEnum::Hearts, PlayingCardSuitPhpEnum::Diamonds => PlayingCardColorPhpEnum::Red,
            PlayingCardSuitPhpEnum::Clubs, PlayingCardSuitPhpEnum::Spades => PlayingCardColorPhpEnum::Black,
        };
    }
    public function getSymbol(): string
    {
        return match($this) {
            PlayingCardSuitPhpEnum::Hearts => 'U+2665',
            PlayingCardSuitPhpEnum::Diamonds => 'U+2666',
            PlayingCardSuitPhpEnum::Clubs => 'U+2663',
            PlayingCardSuitPhpEnum::Spades => 'U+2660',
        };
    }
}
