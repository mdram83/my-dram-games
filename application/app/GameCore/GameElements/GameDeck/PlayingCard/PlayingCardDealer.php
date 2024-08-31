<?php

namespace App\GameCore\GameElements\GameDeck\PlayingCard;

interface PlayingCardDealer
{
    // Not needed, install in dedicated GameServicesProvider to use within specific GamePlay
    public function getEmptyStock(bool $unique = true): CollectionPlayingCardUnique|CollectionPlayingCard;

    public function shuffleAndDealCards(CollectionPlayingCard $stock, array $definitions): void;

    // Not needed, added Collection::keys() method instead.
    public function getCardsByKeys(
        CollectionPlayingCard $deck,
        ?array $keys,
        bool $unique = false,
        bool $strict = false
    ): CollectionPlayingCardUnique|CollectionPlayingCard;

    // Not needed, added Collection::keys() method instead.
    public function getCardsKeys(CollectionPlayingCard $stock): array;

    // Not needed, added PlayingCardCollection::sortByKeys(array $keys) method
    public function getSortedCards(
        CollectionPlayingCard $stock,
        array $keys,
        bool $strict = false
    ): CollectionPlayingCard;

    // Not needed, added Collection::pullFirst() method instead.
    public function pullFirstCard(CollectionPlayingCard $stock, bool $strict = false): ?PlayingCard;

    public function moveCardsByKeys(
        CollectionPlayingCard $fromStock,
        CollectionPlayingCard $toStock,
        array $keys
    ): void;

    public function moveCardsTimes(
        CollectionPlayingCard $fromStock,
        CollectionPlayingCard $toStock,
        int $numberOfCards,
        bool $strict = false
    ): void;

    public function collectCards(CollectionPlayingCard $toStock, array $fromStocks): CollectionPlayingCard;

    // Not needed, utilize PlayingCardCollection::countMatchingKeyCombinations
    public function hasStockAnyCombination(CollectionPlayingCard $stock, array $combinations): bool;

    // Moved to PlayingCardCollection::countMatchingKeyCombinations
    public function countStockMatchingCombinations(CollectionPlayingCard $stock, array $combinations): int;
}
