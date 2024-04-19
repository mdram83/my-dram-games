<?php

namespace App\Games\Thousand\Elements;

use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameElements\GameMove\GameMoveException;
use App\GameCore\Player\Player;

abstract class GameMoveThousand implements GameMove
{
    /**
     * @throws GameMoveException
     */
    public function __construct(
        readonly protected Player $player,
        readonly protected array $details,
        readonly protected ?GamePhaseThousand $phase = null
    )
    {
        if (!$this->isValidInput()) {
            throw new GameMoveException(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);
        }
    }

    abstract protected function isValidInput(): bool;

    final public function getPlayer(): Player
    {
        return $this->player;
    }

    final public function getDetails(): array
    {
        return array_merge($this->details, ['phase' => $this->phase]);
    }

    protected function hasPhase(): bool
    {
        return isset($this->phase);
    }
}
