<?php

namespace App\Games\TicTacToe;

use App\GameCore\GameResult\GameResult;
use App\GameCore\GameResult\GameResultException;

class GameResultTicTacToe implements GameResult
{
    public const MESSAGE_WIN = 'won the Game!';
    public const MESSAGE_DRAW = 'Draw!';

    /**
     * @throws GameResultException
     */
    public function __construct(
        readonly private ?string $winner = null,
        readonly array $winningFields = [],
    )
    {
        $this->validateInputs();
    }

    public function getMessage(): string
    {
        if (isset($this->winner)) {
            return $this->winner . ' ' . $this::MESSAGE_WIN;
        }
        return $this::MESSAGE_DRAW;
    }

    public function getDetails(): array
    {
        return [
            'winnerName' => $this->winner,
            'winningFields' => array_map(fn($field) => (string) $field, $this->winningFields),
        ];
    }

    public function toArray(): array
    {
        return $this->getDetails();
    }

    /**
     * @throws GameResultException
     */
    private function validateInputs(): void
    {
        if (
            (isset($this->winner) && $this->winningFields === [])
            || ($this->winningFields !== [] && !isset($this->winner))
            || ($this->winningFields !== [] && count($this->winningFields) !== 3)
        ) {
            throw new GameResultException(GameResultException::MESSAGE_INCORRECT_PARAMETER);
        }
    }
}
