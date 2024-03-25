<?php

namespace App\Games\TicTacToe;

use App\GameCore\GameElements\GameCharacter\GameCharacterException;
use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GamePlay\GamePlay;
use App\GameCore\GamePlay\GamePlayBase;
use App\GameCore\Player\Player;

class GamePlayTicTacToe extends GamePlayBase implements GamePlay
{
    protected Player $activePlayer;
    protected CollectionGameCharacterTicTacToe $characters;
    protected GameBoardTicTacToe $board;

    public function handleMove(GameMove $move): void
    {
        // TODO: Implement handleMove() method.
        // check if valid player (or handle through exception)
        // check if current player (see above)
        // check if valid move
        // update board
        // check win
        // update active player
        // save data
    }

    public function getSituation(Player $player): array
    {
        return [
            'players' => [
                $this->storage->getGameInvite()->getPlayers()[0]->getName(),
                $this->storage->getGameInvite()->getPlayers()[1]->getName(),
            ],
            'activePlayer' => $this->activePlayer->getName(),
            'characters' => [
                'x' => $this->characters->getOne('x')->getPlayer()->getName(),
                'o' => $this->characters->getOne('o')->getPlayer()->getName(),
            ],
            'board' => json_decode($this->board->toJson(), true),
        ];
    }

    /**
     * @throws GameCharacterException
     */
    protected function initialize(): void
    {
        $players = $this->storage->getGameInvite()->getPlayers();

        $this->setActivePlayer($players[0]);
        $this->setCharacters($players[0], $players[1]);
        $this->setBoard();

        $this->saveData();
    }

    protected function saveData(): void
    {
        $this->storage->setGameData([
            'activePlayerId' => $this->activePlayer->getId(),
            'characters' => [
                'x' => $this->characters->getOne('x')->getPlayer()->getId(),
                'o' => $this->characters->getOne('o')->getPlayer()->getId(),
            ],
            'board' => $this->board->toJson(),
        ]);
    }

    /**
     * @throws GameCharacterException
     */
    protected function loadData(): void
    {
        $data = $this->storage->getGameData();

        $this->setActivePlayer($this->players->getOne($data['activePlayerId']));
        $this->setCharacters(
            $this->players->getOne($data['characters']['x']),
            $this->players->getOne($data['characters']['o'])
        );
        $this->setBoard($data['board']);
    }

    private function setActivePlayer(Player $player): void
    {
        $this->activePlayer = $player;
    }

    /**
     * @throws GameCharacterException
     */
    private function setCharacters(Player $playerX, Player $playerO): void
    {
        $this->characters = new CollectionGameCharacterTicTacToe(
            (clone $this->collectionPlayersHandler)->reset(),
            [new GameCharacterTicTacToe('x', $playerX), new GameCharacterTicTacToe('o', $playerO)],
        );
    }

    private function setBoard(?string $json = null): void
    {
        $this->board = new GameBoardTicTacToe();

        if (isset($json)) {
            $this->board->setFromJson($json);
        }
    }
}
