<?php

namespace App\Games\Thousand\Tools;

use App\GameCore\GameElements\GameDeck\PlayingCard\CollectionPlayingCardUnique;
use MyDramGames\Utils\Player\Player;

class PlayerDataThousand
{
    public function __construct(Player $player)
    {
        $this->playerId = $player->getId();
    }

    readonly private int|string $playerId;

    public int $seat;

    public CollectionPlayingCardUnique $hand;
    public CollectionPlayingCardUnique $tricks;

    public int|string|null $bid = null;
    public bool $ready = true;
    public bool $barrel = false;
    public array $points = [];
    public array $bombRounds = [];
    public array $trumps = [];

    public function getId(): int|string
    {
        return $this->playerId;
    }

    public function toArray(): array
    {
        return [
            'seat' => $this->seat,
            'hand' => $this->hand,
            'tricks' => $this->tricks,
            'bid' => $this->bid,
            'ready' => $this->ready,
            'barrel' => $this->barrel,
            'points' => $this->points,
            'bombRounds' => $this->bombRounds,
            'trumps' => $this->trumps,
        ];
    }
}
