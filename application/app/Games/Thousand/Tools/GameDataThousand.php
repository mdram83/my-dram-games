<?php

namespace App\Games\Thousand\Tools;

use App\GameCore\GameElements\GameDeck\PlayingCard\CollectionPlayingCardUnique;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardSuit;
use App\GameCore\GameElements\GamePhase\GamePhase;
use MyDramGames\Utils\Player\Player;

class GameDataThousand
{
    public Player $dealer;
    public Player $obligation;

    public ?Player $bidWinner;
    public int $bidAmount;

    public CollectionPlayingCardUnique $stock;
    public CollectionPlayingCardUnique $stockRecord;
    public CollectionPlayingCardUnique $table;
    public CollectionPlayingCardUnique $deck;

    public int $round;
    public ?PlayingCardSuit $trumpSuit;
    public ?PlayingCardSuit $turnSuit;
    public ?Player $turnLead;
    public GamePhase $phase;

    public function advanceGamePhase(bool $lastAttempt): void
    {
        $this->phase = $this->phase->getNextPhase($lastAttempt);
    }
}
