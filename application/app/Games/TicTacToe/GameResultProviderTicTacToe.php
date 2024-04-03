<?php

namespace App\Games\TicTacToe;

use App\GameCore\GameElements\GameBoard\GameBoardException;
use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameRecord\CollectionGameRecord;
use App\GameCore\GameRecord\GameRecordFactory;
use App\GameCore\GameResult\GameResult;
use App\GameCore\GameResult\GameResultException;
use App\GameCore\GameResult\GameResultProvider;
use App\GameCore\GameResult\GameResultProviderException;
use App\GameCore\Services\Collection\Collection;
use App\GameCore\Services\Collection\CollectionException;
use App\Games\TicTacToe\Elements\CollectionGameCharacterTicTacToe;
use App\Games\TicTacToe\Elements\GameBoardTicTacToe;

class GameResultProviderTicTacToe implements GameResultProvider
{
    private const MOVES_PREDICTION = 2;

    readonly private GameRecordFactory $recordFactory;
    readonly private Collection $handler;
    private CollectionGameCharacterTicTacToe $characters;
    private array $winningFields = [];
    private ?string $winningValue = null;

    private bool $resultProvided = false;
    private bool $recordsCreated = false;

    public function __construct(Collection $handler, GameRecordFactory $recordFactory)
    {
        $this->handler = $handler;
        $this->recordFactory = $recordFactory;
    }

    /**
     * @throws GameResultProviderException|GameResultException|GameBoardException
     */
    public function getResult(mixed $data): ?GameResult
    {
        if ($this->resultProvided) {
            throw new GameResultProviderException(GameResultProviderException::MESSAGE_RESULTS_ALREADY_SET);
        }

        $this->validateData($data);

        $this->characters = $data['characters'];
        $board = $data['board'];

        if ($this->checkWin($board)) {
            $this->resultProvided = true;
            return new GameResultTicTacToe(
                $this->characters->getOne($this->winningValue)->getPlayer()->getName(),
                $this->winningFields
            );
        }

        if ($this->checkDraw(clone $board, $data['nextMoveCharacterName'])) {
            $this->resultProvided = true;
            return new GameResultTicTacToe();
        }

        return null;
    }

    /**
     * @throws GameResultProviderException
     * @throws CollectionException
     */
    public function createGameRecords(GameInvite $gameInvite): CollectionGameRecord
    {
        if (!$this->resultProvided) {
            throw new GameResultProviderException(GameResultProviderException::MESSAGE_RESULT_NOT_SET);
        }

        if ($this->recordsCreated) {
            throw new GameResultProviderException(GameResultProviderException::MESSAGE_RECORD_ALREADY_SET);
        }

        $recordsCollection = new CollectionGameRecord(clone $this->handler, []);

        foreach (['x', 'o'] as $characterName) {
            $record = $this->recordFactory->create(
                $gameInvite,
                $this->characters->getOne($characterName)->getPlayer(),
                $this->winningValue === $characterName,
                ['character' => $characterName]
            );
            $recordsCollection->add($record);
        }

        $this->recordsCreated = true;

        return $recordsCollection;
    }

    /**
     * @throws GameResultProviderException
     */
    private function validateData(mixed $data): void
    {
        if (
            !isset($data['board'])
            || !($data['board'] instanceof GameBoardTicTacToe)
            || !isset($data['characters'])
            || !($data['characters'] instanceof CollectionGameCharacterTicTacToe)
            || !isset($data['nextMoveCharacterName'])
            || !in_array($data['nextMoveCharacterName'], ['x', 'o'])
        ) {
            throw new GameResultProviderException(GameResultProviderException::MESSAGE_INCORRECT_DATA_PARAMETER);
        }
    }

    private function checkWin(GameBoardTicTacToe $board): bool
    {
        for ($i = 1; $i <= 7; $i += 3) {
            if ($this->checkWinForKeys($board, [$i, $i + 1, $i + 2])) {
                return true;
            }
        }

        for ($i = 1; $i < 4; $i++) {
            if ($this->checkWinForKeys($board, [$i, $i + 3, $i + 6])) {
                return true;
            }
        }

        if ($this->checkWinForKeys($board, [1, 5, 9]) || $this->checkWinForKeys($board, [3, 5, 7])) {
            return true;
        }

        return false;
    }

    private function checkWinForKeys(GameBoardTicTacToe $board, array $keys): bool
    {
        $values = array_unique(array_values([
            $board->getFieldValue((string) $keys[0]),
            $board->getFieldValue((string) $keys[1]),
            $board->getFieldValue((string) $keys[2]),
        ]));

        if (count($values) === 1 && $values[0] !== null) {
            $this->winningFields = $keys;
            $this->winningValue = $values[0];
            return true;
        }

        return false;
    }

    /**
     * @throws GameBoardException
     */
    private function checkDraw(GameBoardTicTacToe $board, string $nextCharacterName): bool
    {
        $remainingKeys = array_keys(
            array_filter(json_decode($board->toJson(), true), fn($field) => $field === null)
        );

        if (count($remainingKeys) > $this::MOVES_PREDICTION) {
            return false;
        }

        foreach ($remainingKeys as $fieldKey) {

            $updatedBoard = clone $board;
            $updatedBoard->setFieldValue((string) $fieldKey, $nextCharacterName);

            if ($this->checkWin($updatedBoard)) {
                $this->winningFields = [];
                $this->winningValue = null;
                return false;
            }

            if (!$this->checkDraw(clone $updatedBoard, $this->getNextCharacterName($nextCharacterName))) {
                return false;
            }
        }

        return true;
    }

    private function getNextCharacterName(string $currentCharacterName): string
    {
        return $currentCharacterName === 'x' ? 'o' : 'x';
    }
}
