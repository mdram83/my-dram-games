<?php

namespace App\Games\TicTacToe;

use App\GameCore\GameElements\GameBoard\GameBoardException;
use App\GameCore\GameResult\GameResult;
use App\GameCore\GameResult\GameResultException;
use App\GameCore\GameResult\GameResultProvider;
use App\GameCore\GameResult\GameResultProviderException;
use App\Games\GameResultTicTacToe;

class GameResultProviderTicTacToe implements GameResultProvider
{
    private const MOVES_PREDICTION = 2;

    private array $winningFields = [];
    private ?string $winningValue = null;

    /**
     * @throws GameResultProviderException|GameResultException|GameBoardException
     */
    public function getResult(mixed $data): ?GameResult
    {
        $this->validateData($data);

        $board = $data['board'];

        if ($this->checkWin($board)) {
            return new GameResultTicTacToe(
                $data['characters']->getOne($this->winningValue)->getPlayer()->getName(),
                $this->winningFields
            );
        }

        if ($this->checkDraw(clone $board, $data['nextMoveCharacterName'])) {
            return new GameResultTicTacToe();
        }

        return null;
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

        $hasWin = false;

        foreach ($remainingKeys as $fieldKey) {

            $updatedBoard = clone $board;
            $updatedBoard->setFieldValue((string) $fieldKey, $nextCharacterName);

            if ($this->checkWin($updatedBoard)) {
                $this->winningFields = [];
                $this->winningValue = null;
                return false;
            }

            $hasWin = !$this->checkDraw(clone $updatedBoard, $this->getNextCharacterName($nextCharacterName));
        }

        return !$hasWin;
    }

    private function getNextCharacterName(string $currentCharacterName): string
    {
        return $currentCharacterName === 'x' ? 'o' : 'x';
    }

}
