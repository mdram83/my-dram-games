<?php

namespace App\Games\Thousand;

use App\GameCore\GameResult\GameResult;
use App\GameCore\GameResult\GameResultException;
use App\GameCore\Player\Player;

class GameResultThousand implements GameResult
{
    public const MESSAGE_WIN = 'won the Game!';
    public const MESSAGE_FORFEIT = 'left the Game.';

    /**
     * @throws GameResultException
     */
    public function __construct(
        readonly private array $points,
        readonly private ?Player $winner = null,
        readonly private ?Player $forfeited = null,
    )
    {
        $this->validateInputs();
    }

    public function getMessage(): string
    {
        if (isset($this->winner)) {
            return $this->winner->getName() . ' ' . $this::MESSAGE_WIN;
        }
        return $this->forfeited->getName() . ' ' . $this::MESSAGE_FORFEIT;
    }

    public function getDetails(): array
    {
        return [
            'winnerName' => $this->winner?->getName(),
            'points' => $this->points,
            'forfeitedName' => $this->forfeited?->getName(),
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
        if (!$this->hasValidPoints() || !$this->hasValidPlayers()) {
            throw new GameResultException(GameResultException::MESSAGE_INCORRECT_PARAMETER);
        }
    }

    private function hasValidPoints(): bool
    {
        foreach ($this->points as $seat => $points) {
            if (
                !$this->isValidSeatNumber($seat)
                || !$this->isValidPlayerName($points['playerName'] ?? null)
                || !$this->isValidPointsValue($points['points'] ?? null)
            ) {
                return false;
            }
        }

        return true;
    }

    private function isValidSeatNumber(mixed $seat): bool
    {
        return (isset($seat) && is_int($seat) && $seat >= 1 && $seat <= 4);
    }

    private function isValidPlayerName(mixed $playerName): bool
    {
        return (isset($playerName)  && is_string($playerName) && $playerName !== '');
    }

    private function isValidPointsValue(mixed $pointsValue): bool
    {
        return (isset($pointsValue) && is_int($pointsValue));
    }

    private function hasValidPlayers(): bool
    {
        return (
            isset($this->winner) && $this->isPlayerInPointsList($this->winner)
            || isset($this->forfeited) && $this->isPlayerInPointsList($this->forfeited)
        );

    }

    private function isPlayerInPointsList(Player $player): bool
    {
        $playerNames = array_map(fn($point) => $point['playerName'], $this->points);
        return in_array($player->getName(), $playerNames);
    }
}
