<?php

namespace App\Models\GameCore\Game;

use App\Models\GameCore\GameDefinition\GameDefinition;
use App\Models\GameCore\Player\Player;

interface Game
{
    public function getId(): int|string;
    public function addPlayer(Player $player, bool $host = false): void;
    public function getPlayers(): array;
    public function isPlayerAdded(Player $player): bool;
    public function getHost(): Player;
    public function isHost(Player $player): bool;
    public function setNumberOfPlayers(int $numberOfPlayers): void;
    public function getNumberOfPlayers(): int;
    public function setGameDefinition(GameDefinition $gameDefinition): void;
    public function getGameDefinition(): GameDefinition;
    public function toArray():array;
}
