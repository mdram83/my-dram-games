<?php

namespace App\GameCore\GamePlay;

use App\GameCore\Services\Collection\CollectionGamePlayPlayers;

interface GamePlayStorage
{
    public function getId(): int|string;
    public function setPlayers(CollectionGamePlayPlayers $players): void;
    public function getPlayers(): CollectionGamePlayPlayers;
    public function setBoard(GameBoard $board): void;
    public function getBoard(): GameBoard;
}
