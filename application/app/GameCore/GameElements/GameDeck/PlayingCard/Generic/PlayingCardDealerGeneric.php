<?php

namespace App\GameCore\GameElements\GameDeck\PlayingCard\Generic;

use App\GameCore\GameElements\GameDeck\PlayingCard\CollectionPlayingCard;
use App\GameCore\GameElements\GameDeck\PlayingCard\CollectionPlayingCardUnique;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCard;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDealer;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDealerException;
use App\GameCore\Services\Collection\Collection;
use App\GameCore\Services\Collection\CollectionException;

class PlayingCardDealerGeneric implements PlayingCardDealer
{
    public function __construct(
        private readonly Collection $collectionHandler
    )
    {

    }

    public function getEmptyStock(bool $unique = true): CollectionPlayingCardUnique|CollectionPlayingCard
    {
        if ($unique) {
            return new CollectionPlayingCardUnique(clone $this->collectionHandler);
        }

        return new CollectionPlayingCard(clone $this->collectionHandler);
    }

    /**
     * @throws PlayingCardDealerException
     * @throws CollectionException
     */
    public function shuffleAndDealCards(CollectionPlayingCard $stock, array $definitions): void
    {
        $this->validateDealDefinitions($definitions);
        $this->validateDealEnoughCards($definitions, $stock->count());

        $stock->shuffle();
        $this->sortDealDefinitions($definitions);

        foreach (array_filter($definitions, fn($definition) => isset($definition['numberOfCards'])) as $definition) {
            $this->moveCardsTimes($stock, $definition['stock'], $definition['numberOfCards']);
        }

        if ($nullable = array_filter($definitions, fn($definition) => $definition['numberOfCards'] === null)) {
            while ($stock->count() > 0) {
                current($nullable)['stock']->add($this->pullFirstCard($stock));
                if (!next($nullable)) {
                    reset($nullable);
                }
            }
        }
    }

    /**
     * @throws PlayingCardDealerException
     * @throws CollectionException
     */
    public function getCardsByKeys(
        CollectionPlayingCard $deck,
        ?array $keys,
        bool $unique = false,
        bool $strict = false
    ): CollectionPlayingCardUnique|CollectionPlayingCard
    {
        if ($unique && count($keys) !== count(array_unique($keys))) {
            throw new PlayingCardDealerException(PlayingCardDealerException::MESSAGE_NOT_UNIQUE_KEYS);
        }

        if ($strict) {
            foreach ($keys as $key) {
                if (!$deck->exist($key)) {
                    throw new PlayingCardDealerException(PlayingCardDealerException::MESSAGE_KEY_MISSING_IN_STOCK);
                }
            }
        }

        return $this->getEmptyStock($unique)->reset(array_map(fn($cardKey) => $deck->getOne($cardKey), $keys ?? []));
    }

    public function getCardsKeys(CollectionPlayingCard $stock): array
    {
        return array_keys($stock->toArray());
    }

    public function getSortedCards(CollectionPlayingCard $stock, array $keys, bool $strict = false): CollectionPlayingCard
    {
        // TODO: Implement getSortedCards() method.
    }

    /**
     * @throws PlayingCardDealerException
     */
    public function pullFirstCard(CollectionPlayingCard $stock, bool $strict = false): ?PlayingCard
    {
        if ($stock->count() === 0) {
            if ($strict) {
                throw new PlayingCardDealerException(PlayingCardDealerException::MESSAGE_NOT_ENOUGH_IN_STOCK);
            }
            return null;
        }
        return $stock->pullFirst();
    }

    public function moveCardsByKeys(CollectionPlayingCard $fromStock, CollectionPlayingCard $toStock, array $keys): void
    {
        // TODO: Implement moveCardsByKeys() method.
    }

    /**
     * @throws CollectionException
     * @throws PlayingCardDealerException
     */
    public function moveCardsTimes(CollectionPlayingCard $fromStock, CollectionPlayingCard $toStock, int $numberOfCards, bool $strict = false): void
    {
        for ($i = 1; $i <= $numberOfCards; $i++) {
            if ($card = $this->pullFirstCard($fromStock, $strict)) {
                $toStock->add($card);
            } else {
                break;
            }
        }
    }

    public function collectCards(CollectionPlayingCard $toStock, array $fromStocks): CollectionPlayingCard
    {
        // TODO: Implement collectCards() method.
    }

    public function hasStockAnyCombination(CollectionPlayingCard $stock, array $combinations): bool
    {
        // TODO: Implement hasStockAnyCombination() method.
    }

    /**
     * @throws PlayingCardDealerException
     */
    private function validateDealDefinitions(array $distributionDefinitions): void
    {
        if ($distributionDefinitions === []) {
            throw new PlayingCardDealerException(PlayingCardDealerException::MESSAGE_DISTRIBUTION_DEFINITION);
        }

        foreach ($distributionDefinitions as $definition) {
            if (
                !isset($definition['stock'])
                || !$definition['stock'] instanceof CollectionPlayingCard
                ||!in_array('numberOfCards', array_keys($definition))
                || ($definition['numberOfCards'] !== null & !is_int($definition['numberOfCards']))
            ) {
                throw new PlayingCardDealerException(PlayingCardDealerException::MESSAGE_DISTRIBUTION_DEFINITION);
            }
        }
    }

    /**
     * @throws PlayingCardDealerException
     */
    private function validateDealEnoughCards(array $definitions, int $requested): void
    {
        $dealCounter = 0;

        foreach ($definitions as $definition) {
            $dealCounter += $definition['numberOfCards'] ?? 0;
        }

        if ($dealCounter > $requested) {
            throw new PlayingCardDealerException(PlayingCardDealerException::MESSAGE_NOT_ENOUGH_TO_DEAL);
        }
    }

    private function sortDealDefinitions(array &$definitions): void
    {
        $numbersOfCards = array_column($definitions, 'numberOfCards');
        array_multisort($numbersOfCards, SORT_DESC, $definitions);
    }
}
