<?php

namespace App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum;

use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardException;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardSuit;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardSuitRepository;

class PlayingCardSuitRepositoryPhpEnum implements PlayingCardSuitRepository
{
    /**
     * @throws PlayingCardException
     */
    public function getOne(string $key): PlayingCardSuit
    {
        if (!$suit = PlayingCardSuitPhpEnum::tryFrom($key)) {
            throw new PlayingCardException(PlayingCardException::MESSAGE_MISSING_SUIT);
        }

        return $suit;
    }
}
