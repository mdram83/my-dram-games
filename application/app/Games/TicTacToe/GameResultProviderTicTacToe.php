<?php

namespace App\Games\TicTacToe;

use App\GameCore\GameResult\GameResult;
use App\GameCore\GameResult\GameResultException;
use App\GameCore\GameResult\GameResultProvider;
use App\GameCore\GameResult\GameResultProviderException;
use App\Games\GameResultTicTacToe;

class GameResultProviderTicTacToe implements GameResultProvider
{
    private GameBoardTicTacToe $board;
    private CollectionGameCharacterTicTacToe $characters;

    private array $winningFields = [];
    private ?string $winningValue = null;

    /**
     * @throws GameResultProviderException|GameResultException
     */
    public function getResult(mixed $data): ?GameResult
    {
        $this->validateData($data);
        $this->board = $data['board'];
        $this->characters = $data['characters'];

        if ($this->checkWin()) {
            return new GameResultTicTacToe(
                $this->characters->getOne($this->winningValue)->getPlayer()->getName(),
                $this->winningFields
            );
        }

        if ($this->checkDraw()) {
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
        ) {
            throw new GameResultProviderException(GameResultProviderException::MESSAGE_INCORRECT_DATA_PARAMETER);
        }
    }

    private function checkWin(): bool
    {
        for ($i = 1; $i <= 7; $i += 3) {
            if ($this->checkWinForKeys([$i, $i + 1, $i + 2])) {
                return true;
            }
        }

        for ($i = 1; $i < 4; $i++) {
            if ($this->checkWinForKeys([$i, $i + 3, $i + 6])) {
                return true;
            }
        }

        if ($this->checkWinForKeys([1, 5, 9]) || $this->checkWinForKeys([3, 5, 7])) {
            return true;
        }

        return false;
    }

    private function checkWinForKeys(array $keys): bool
    {
        $values = array_unique(array_values([
            $this->board->getFieldValue((string) $keys[0]),
            $this->board->getFieldValue((string) $keys[1]),
            $this->board->getFieldValue((string) $keys[2]),
        ]));

        if (count($values) === 1 && $values[0] !== null) {
            $this->winningFields = $keys;
            $this->winningValue = $values[0];
            return true;
        }
        return false;
    }

    private function checkDraw(): bool
    {
        $remainingFields = array_filter(json_decode($this->board->toJson(), true), fn($field) => $field === null);

        if (count($remainingFields) > 2) {
            return false;
        }

        // TODO check who's next (need to pass in getResult $data array
        // TODO check who's next and validate all possible move combinations, then utilize checkWin function (but reset winner and fields properties at the end)
        // TODO if you cant get win for all possible combinations this means there is a draw

    }

}
