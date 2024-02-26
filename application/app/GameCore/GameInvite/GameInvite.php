<?php

namespace App\GameCore\GameInvite;

use App\GameCore\GameBox\GameBox;
use App\GameCore\GameSetup\GameSetup;
use App\GameCore\Player\Player;

interface GameInvite
{
    public function getId(): int|string;
    public function addPlayer(Player $player, bool $host = false): void;
    public function getPlayers(): array;
    public function isPlayerAdded(Player $player): bool;
    public function getHost(): Player;
    public function isHost(Player $player): bool;
    public function setOptions(array $options): void;
    public function getGameSetup(): GameSetup;
    public function setGameBox(GameBox $gameBox): void;
    public function getGameBox(): GameBox;
    public function toArray():array;
}
