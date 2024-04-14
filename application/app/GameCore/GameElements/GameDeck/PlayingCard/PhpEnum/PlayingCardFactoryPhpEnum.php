<?php

namespace App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum;

use App\GameCore\GameElements\GameDeck\PlayingCard\Generic\PlayingCardGeneric;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCard;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardException;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardFactory;

class PlayingCardFactoryPhpEnum implements PlayingCardFactory
{
    /**
     * @throws PlayingCardException
     */
    public function create(string $key): PlayingCard
    {
        [$rank, $suit, $color] = $this->getValidatedParams($key);

        return new PlayingCardGeneric($rank, $suit ?? null, $color ?? null);
    }

    /**
     * @throws PlayingCardException
     */
    private function getValidatedParams(string $key): array
    {
        [$rankKey, $suitOrColorKey] = explode(PlayingCardGeneric::PLAYING_CARD_KEY_SEPARATOR, $key, 2);

        if (!$rank = PlayingCardRankPhpEnum::tryFrom($rankKey)) {
            throw new PlayingCardException(PlayingCardException::MESSAGE_MISSING_RANK);
        }

        if ($rank->isJoker() && !$color = array_filter(PlayingCardColorPhpEnum::cases(), fn($color) => $color->getName() === $suitOrColorKey)[0] ?? null) {
            throw new PlayingCardException(PlayingCardException::MESSAGE_MISSING_COLOR);
        }

        if (!$rank->isJoker() && !$suit = PlayingCardSuitPhpEnum::tryFrom($suitOrColorKey)) {
            throw new PlayingCardException(PlayingCardException::MESSAGE_MISSING_SUIT);
        }

        return [$rank, $suit ?? null, $color ?? null];
    }
}
