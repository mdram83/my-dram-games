<?php

namespace App\Games\Thousand;

use App\GameCore\GameElements\GameBoard\GameBoardException;
use App\GameCore\GameElements\GameCharacter\GameCharacterException;
use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GamePlay\GamePlay;
use App\GameCore\GamePlay\GamePlayBase;
use App\GameCore\GamePlay\GamePlayException;
use App\GameCore\GameResult\GameResultException;
use App\GameCore\GameResult\GameResultProviderException;
use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\CollectionException;

class GamePlayThousand extends GamePlayBase implements GamePlay
{
    protected Player $activePlayer;
//    protected ?GameResultTicTacToe $result = null;

    /**
     * @throws GamePlayException
     * @throws GameBoardException
     */
    public function handleMove(GameMove $move): void
    {
        if ($this->isFinished()) {
            throw new GamePlayException(GamePlayException::MESSAGE_MOVE_ON_FINISHED_GAME);
        }

//        $this->validateMove($move);

        // handle move/phase
//        $this->setActivePlayer($this->getNextPlayer($move->getPlayer()));
//        $this->saveData();

//        $resultProvider = new GameResultProviderTicTacToe(clone $this->collectionHandler, $this->gameRecordFactory);

//        if ($this->result = $resultProvider->getResult([
//            'board' => $this->board,
//            'characters' => $this->characters,
//            'nextMoveCharacterName' => $this->getPlayerCharacterName($this->activePlayer),
//        ])) {
//            $resultProvider->createGameRecords($this->getGameInvite());
//            $this->storage->setFinished();
//        }
    }

    /**
     * @throws GamePlayException|GameResultProviderException|GameResultException|GameBoardException|CollectionException
     */
    public function handleForfeit(Player $player): void
    {
        if (!$this->getPlayers()->exist($player->getId())) {
            throw new GamePlayException(GamePlayException::MESSAGE_NOT_PLAYER);
        }

        if ($this->isFinished()) {
            throw new GamePlayException(GamePlayException::MESSAGE_MOVE_ON_FINISHED_GAME);
        }

//        $resultProvider = new GameResultProviderTicTacToe(clone $this->collectionHandler, $this->gameRecordFactory);
//
//        $this->result = $resultProvider->getResult(
//            ['forfeitCharacter' => $this->getPlayerCharacterName($player), 'characters' => $this->characters]
//        );
//        $resultProvider->createGameRecords($this->getGameInvite());
//        $this->storage->setFinished();
    }

    public function getSituation(Player $player): array
    {
//        $resultArray = isset($this->result)
//            ? ['result' => ['details' => $this->result->getDetails(), 'message' => $this->result->getMessage()]]
//            : [];
//
//        return array_merge(
//            [
//                'players' => [
//                    $this->storage->getGameInvite()->getPlayers()[0]->getName(),
//                    $this->storage->getGameInvite()->getPlayers()[1]->getName(),
//                ],
//                'activePlayer' => $this->activePlayer->getName(),
//                'characters' => [
//                    'x' => $this->characters->getOne('x')->getPlayer()->getName(),
//                    'o' => $this->characters->getOne('o')->getPlayer()->getName(),
//                ],
//                'board' => json_decode($this->board->toJson(), true),
//                'isFinished' => $this->isFinished(),
//            ],
//            $resultArray
//        );
        return [];
    }

    /**
     * @throws GameCharacterException
     */
    protected function initialize(): void
    {
        $players = $this->storage->getGameInvite()->getPlayers();

//        $this->setActivePlayer($players[0]);

//        $this->saveData();
    }

    protected function saveData(): void
    {
//        $this->storage->setGameData([
//            'activePlayerId' => $this->activePlayer->getId(),
//            'characters' => [
//                'x' => $this->characters->getOne('x')->getPlayer()->getId(),
//                'o' => $this->characters->getOne('o')->getPlayer()->getId(),
//            ],
//            'board' => $this->board->toJson(),
//        ]);
    }

    /**
     * @throws GameCharacterException
     */
    protected function loadData(): void
    {
        $data = $this->storage->getGameData();

//        $this->setActivePlayer($this->players->getOne($data['activePlayerId']));
//        $this->setCharacters(
//            $this->players->getOne($data['characters']['x']),
//            $this->players->getOne($data['characters']['o'])
//        );
//        $this->setBoard($data['board']);
    }

    private function setActivePlayer(Player $player): void
    {
        $this->activePlayer = $player;
    }

    /**
     * @throws GamePlayException
     */
    private function validateMove(GameMove $move): void
    {
//        if (!is_a($move, GameMoveTicTacToe::class)) {
//            throw new GamePlayException(GamePlayException::MESSAGE_INCOMPATIBLE_MOVE);
//        }
//
//        if ($move->getPlayer()->getId() !== $this->activePlayer->getId()) {
//            throw new GamePlayException(GamePlayException::MESSAGE_NOT_CURRENT_PLAYER);
//        }
    }

//    private function getPlayerCharacterName(Player $player): string
//    {
//        return $this->characters
//            ->filter(fn($value, $key) => $value->getPlayer()->getId() === $player->getId())
//            ->pullFirst()
//            ->getName();
//    }

//    private function getNextPlayer(Player $currentPlayer): Player
//    {
//        return $this->characters
//            ->filter(fn($value, $key) => $value->getPlayer()->getId() !== $currentPlayer->getId())
//            ->pullFirst()
//            ->getPlayer();
//    }
}
