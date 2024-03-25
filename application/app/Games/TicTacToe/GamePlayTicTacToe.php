<?php

namespace App\Games\TicTacToe;

use App\GameCore\GameElements\GameBoard\GameBoardException;
use App\GameCore\GameElements\GameCharacter\GameCharacterException;
use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GamePlay\GamePlay;
use App\GameCore\GamePlay\GamePlayBase;
use App\GameCore\GamePlay\GamePlayException;
use App\GameCore\Player\Player;

class GamePlayTicTacToe extends GamePlayBase implements GamePlay
{
    protected Player $activePlayer;
    protected CollectionGameCharacterTicTacToe $characters;
    protected GameBoardTicTacToe $board;

    /**
     * @throws GamePlayException
     * @throws GameBoardException
     */
    public function handleMove(GameMove $move): void
    {
        $this->validateMove($move);

        $this->board->setFieldValue($move->getDetails()['fieldKey'], $this->getPlayerCharacterName($move->getPlayer()));
        $this->setActivePlayer($this->getNextPlayer($move->getPlayer()));
        $this->saveData();

        // check win later
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

    /**
     * @throws GamePlayException
     */
    private function validateMove(GameMove $move): void
    {
        if (!is_a($move, GameMoveTicTacToe::class)) {
            throw new GamePlayException(GamePlayException::MESSAGE_INCOMPATIBLE_MOVE);
        }

        if ($move->getPlayer()->getId() !== $this->activePlayer->getId()) {
            throw new GamePlayException(GamePlayException::MESSAGE_NOT_CURRENT_PLAYER);
        }
    }

    private function getPlayerCharacterName(Player $player): string
    {
        return $this->characters
            ->filter(fn($value, $key) => $value->getPlayer()->getId() === $player->getId())
            ->pullFirst()
            ->getName();
    }

    private function getNextPlayer(Player $currentPlayer): Player
    {
        return $this->characters
            ->filter(fn($value, $key) => $value->getPlayer()->getId() !== $currentPlayer->getId())
            ->pullFirst()
            ->getPlayer();
    }
}
