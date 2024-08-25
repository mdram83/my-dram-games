<?php

namespace App\Games\TicTacToe;

use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameElements\GameMove\GameMoveException;
use MyDramGames\Utils\Player\Player;

class GameMoveTicTacToe implements GameMove
{

    /**
     * @throws GameMoveException
     */
    public function __construct(
        readonly private Player $player,
        readonly private int $fieldKey
    )
    {
        if ($this->fieldKey < 1 || $this->fieldKey > 9) {
            throw new GameMoveException(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);
        }
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getDetails(): array
    {
        return ['fieldKey' => $this->fieldKey];
    }
}
