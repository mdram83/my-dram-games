<?php

namespace App\Services\GamePlay;

use MyDramGames\Core\Exceptions\GameBoxException;
use MyDramGames\Core\GameMove\GameMove;
use MyDramGames\Core\GamePlay\GamePlay;
use MyDramGames\Utils\Player\Player;

interface GamePlayService
{
    /**
     * @throws GameBoxException
     */
    public function getGameMove(Player $player, GamePlay $gamePlay, array $moveInputs): GameMove;

    public function dispatchGamePlayMovedEventToAllPlayers(GamePlay $gamePlay): void;

    /**
     * @throws GameBoxException
     */
    public function getShowResponseContent(Player $player, GamePlay $gamePlay): array;
}
