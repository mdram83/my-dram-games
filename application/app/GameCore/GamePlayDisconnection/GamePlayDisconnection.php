<?php

namespace App\GameCore\GamePlayDisconnection;

use MyDramGames\Core\GamePlay\GamePlay;
use MyDramGames\Utils\Player\Player;

interface GamePlayDisconnection
{
    public function setGamePlay(GamePlay $gamePlay): void;
    public function setPlayer(Player $player): void;
    public function setDisconnectedAt(): void;
    public function hasExpired(int $expirationTimeInSeconds): bool;
    public function remove(): void;
}
