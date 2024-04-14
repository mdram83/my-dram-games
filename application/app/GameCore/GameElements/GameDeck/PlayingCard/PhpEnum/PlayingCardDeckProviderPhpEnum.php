<?php

namespace App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum;

use App\GameCore\GameElements\GameDeck\PlayingCard\CollectionPlayingCard;
use App\GameCore\GameElements\GameDeck\PlayingCard\Generic\PlayingCardGeneric;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDeckProvider;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardFactory;
use App\GameCore\Services\Collection\Collection;
use App\GameCore\Services\Collection\CollectionException;

class PlayingCardDeckProviderPhpEnum implements PlayingCardDeckProvider
{
    public function __construct(
        readonly private Collection $handler,
        readonly private PlayingCardFactory $factory,
    )
    {

    }

    /**
     * @throws CollectionException
     */
    public function getDeckSchnapsen(): CollectionPlayingCard
    {
        $deck = new CollectionPlayingCard(clone $this->handler);

        $ranks = [
            PlayingCardRankPhpEnum::Nine,
            PlayingCardRankPhpEnum::Ten,
            PlayingCardRankPhpEnum::Jack,
            PlayingCardRankPhpEnum::Queen,
            PlayingCardRankPhpEnum::King,
            PlayingCardRankPhpEnum::Ace,
        ];

        foreach (PlayingCardSuitPhpEnum::cases() as $suit) {
            foreach ($ranks as $rank) {
                $deck->add($this->factory->create(
                    $rank->getKey() . PlayingCardGeneric::PLAYING_CARD_KEY_SEPARATOR . $suit->getKey()
                ));
            }
        }

        return $deck;
    }
}
