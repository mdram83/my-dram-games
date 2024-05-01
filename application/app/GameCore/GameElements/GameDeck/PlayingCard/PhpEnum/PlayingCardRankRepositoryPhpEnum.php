<?php

namespace App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum;

use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardException;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardRank;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardRankRepository;

class PlayingCardRankRepositoryPhpEnum implements PlayingCardRankRepository
{
    /**
     * @throws PlayingCardException
     */
    public function getOne(string $key): PlayingCardRank
    {
        if (!$suit = PlayingCardRankPhpEnum::tryFrom($key)) {
            throw new PlayingCardException(PlayingCardException::MESSAGE_MISSING_RANK);
        }

        return $suit;
    }
}
