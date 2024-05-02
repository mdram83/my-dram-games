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

        if ($strict && !$this->hasStockAllKeys($deck, $keys)) {
            throw new PlayingCardDealerException(PlayingCardDealerException::MESSAGE_KEY_MISSING_IN_STOCK);
        }

        return $this->getEmptyStock($unique)->reset(array_map(fn($cardKey) => $deck->getOne($cardKey), $keys ?? []));
    }

    public function getCardsKeys(CollectionPlayingCard $stock): array
    {
        return array_keys($stock->toArray());
    }

    /**
     * @throws PlayingCardDealerException
     * @throws CollectionException
     */
    public function getSortedCards(CollectionPlayingCard $stock, array $keys, bool $strict = false): CollectionPlayingCard
    {
        $stockAllKeys = $this->getCardsKeys($stock);

        if ($strict && count(array_diff($stockAllKeys, $keys)) > 0) {
            throw new PlayingCardDealerException(PlayingCardDealerException::MESSAGE_KEYS_NOT_MATCHING_STOCK);
        }

        $stockPriorityKeys = array_intersect($keys, $stockAllKeys);
        $stockRemainingKeys = array_diff($stockAllKeys, $keys);
        $stockOrderedKeys = array_merge($stockPriorityKeys, $stockRemainingKeys);

        return $stock->reset($this->getCardsByKeys($stock, $stockOrderedKeys, false, true)->toArray());
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

    /**
     * @throws PlayingCardDealerException
     * @throws CollectionException
     */
    public function moveCardsByKeys(CollectionPlayingCard $fromStock, CollectionPlayingCard $toStock, array $keys): void
    {
        if (!$this->hasStockAllKeys($fromStock, $keys)) {
            throw new PlayingCardDealerException(PlayingCardDealerException::MESSAGE_KEY_MISSING_IN_STOCK);
        }

        foreach ($keys as $key) {
            $toStock->add($fromStock->getOne($key));
            $fromStock->removeOne($key);
        }
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

    /**
     * @throws PlayingCardDealerException
     * @throws CollectionException
     */
    public function collectCards(CollectionPlayingCard $toStock, array $fromStocks): CollectionPlayingCard
    {
        $this->validateCollectFromStockInputs($fromStocks);

        foreach ($fromStocks as $fromStock) {
            $this->moveCardsTimes($fromStock, $toStock, $fromStock->count(), true);
        }

        return $toStock;
    }


    /**
     * @throws PlayingCardDealerException
     */
    public function hasStockAnyCombination(CollectionPlayingCard $stock, array $combinations): bool
    {
        return $this->countStockMatchingCombinations($stock, $combinations) > 0;
    }

    /**
     * @throws PlayingCardDealerException
     */
    public function countStockMatchingCombinations(CollectionPlayingCard $stock, array $combinations): int
    {
        $this->validateHasStockAnyCombinationInputs($combinations);

        return array_reduce($combinations, function ($carry, $combination) use ($stock) {

            if ($combination === []) {
                return $carry;
            }

            foreach ($combination as $element) {
                if (!$stock->exist($element)) {
                    return $carry;
                }
            }

            return $carry + 1;

        }, 0);
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

    private function hasStockAllKeys(CollectionPlayingCard $stock, array $keys): bool
    {
        foreach ($keys as $key) {
            if (!$stock->exist($key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @throws PlayingCardDealerException
     */
    private function validateCollectFromStockInputs(array $fromStocks): void
    {
        foreach ($fromStocks as $fromStock) {
            if (!$fromStock instanceof CollectionPlayingCard) {
                throw new PlayingCardDealerException(PlayingCardDealerException::MESSAGE_COLLECTION_FROM_INVALID);
            }
        }
    }

    /**
     * @throws PlayingCardDealerException
     */
    private function validateHasStockAnyCombinationInputs(array $combinations): void
    {
        foreach ($combinations as $combination) {
            foreach ($combination as $element) {
                if (!is_string($element) || $element === '') {
                    throw new PlayingCardDealerException(PlayingCardDealerException::MESSAGE_COMBINATION_INVALID);
                }
            }
        }
    }
}
