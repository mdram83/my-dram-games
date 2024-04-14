<?php

namespace App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum;

use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardColor;

enum PlayingCardColorPhpEnum implements PlayingCardColor
{
    case Red;
    case Black;

    public function getName(): string
    {
        return $this->name;
    }
}
