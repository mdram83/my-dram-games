<?php

namespace App\Games\TicTacToe;

use App\GameCore\GamePlay\GameBoard;
use App\GameCore\GamePlay\GameCharacterException;
use App\GameCore\GamePlay\GameMove;
use App\GameCore\GamePlay\GamePlay;
use App\GameCore\GamePlay\GamePlayBase;
use App\GameCore\GamePlay\GameSituation;
use App\GameCore\Player\Player;

class GamePlayTicTacToe extends GamePlayBase implements GamePlay
{
//    protected Player $activePlayer; // TODO update and save in storage when handling move
//    protected CollectionGameCharacterTicTacToe $characters;
//    protected GameBoardTicTacToe $board;

    public function handleMove(Player $player, GameMove $move): void
    {
        // TODO: Implement handleMove() method.
    }

    public function getSituation(Player $player): GameSituation
    {
        // TODO: Implement getStatus() method.
    }

    /**
     * @throws GameCharacterException
     */
    protected function setupGame(): void
    {
//        $players = $this->storage->getGameInvite()->getPlayers();

//        $this->activePlayer = $players[0];
//        $this->storage->setActivePlayer($this->activePlayer); // TODO add method to storage (consider one field for "meta")

//        $this->characters = new CollectionGameCharacterTicTacToe(
//            (clone $this->collectionPlayersHandler)->reset(),
//            [new GameCharacterTicTacToe('x', $players[0]), new GameCharacterTicTacToe('0', $players[1])],
//        );
//        $this->storage->setCharacters($this->characters); // TODO add method to storage (consider one field for "meta")

        $this->storage->setSetup();
    }
}
