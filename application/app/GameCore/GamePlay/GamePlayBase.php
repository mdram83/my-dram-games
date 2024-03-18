<?php

namespace App\GameCore\GamePlay;

use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\CollectionGamePlayPlayers;

abstract class GamePlayBase implements GamePlay
{
    public function __construct(protected GamePlayStorage $storage)
    {

    }

    abstract public function handleMove(GameMove $move): void;
    abstract public function getStatus(Player $player): GameStatus;

    final public function getId(): int|string
    {
        return $this->storage->getId();
    }

    final public function getPlayers(): CollectionGamePlayPlayers
    {
        return $this->storage->getPlayers();
    }
}
