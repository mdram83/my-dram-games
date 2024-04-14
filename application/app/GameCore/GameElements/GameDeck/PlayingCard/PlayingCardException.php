<?php

namespace App\GameCore\GameElements\GameDeck\PlayingCard;

class PlayingCardException extends \Exception
{
    public const MESSAGE_INCORRECT_PARAMS = 'Incorrect card parameters';
    public const MESSAGE_MISSING_RANK = 'Rank is missing';
    public const MESSAGE_MISSING_COLOR = 'Color is missing';
    public const MESSAGE_MISSING_SUIT = 'Suit is missing';
}
