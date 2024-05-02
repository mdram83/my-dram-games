<?php

namespace App\GameCore\GameElements\GameDeck\PlayingCard;

interface PlayingCardDealer
{
    public function getEmptyStock(bool $unique = true): CollectionPlayingCardUnique|CollectionPlayingCard;

    public function shuffleAndDealCards(CollectionPlayingCard $stock, array $definitions): void;

    public function getCardsByKeys(
        CollectionPlayingCard $deck,
        ?array $keys,
        bool $unique = false,
        bool $strict = false
    ): CollectionPlayingCardUnique|CollectionPlayingCard;

    public function getCardsKeys(CollectionPlayingCard $stock): array;

    public function getSortedCards(
        CollectionPlayingCard $stock,
        array $keys,
        bool $strict = false
    ): CollectionPlayingCard;

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

    public function hasStockAnyCombination(CollectionPlayingCard $stock, array $combinations): bool;

    public function countStockMatchingCombinations(CollectionPlayingCard $stock, array $combinations): int;
}
