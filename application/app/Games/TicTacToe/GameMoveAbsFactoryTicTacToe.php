<?php

namespace App\Games\TicTacToe;

use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameElements\GameMove\GameMoveAbsFactory;
use App\GameCore\GameElements\GameMove\GameMoveException;
use MyDramGames\Utils\Player\Player;

class GameMoveAbsFactoryTicTacToe implements GameMoveAbsFactory
{
    /**
     * @throws GameMoveException
     */
    public function create(Player $player, array $inputs): GameMove
    {
        $fieldKey = isset($inputs['fieldKey']) ? (int) $inputs['fieldKey'] : null;
        $this->validateFieldKey($fieldKey);

        return new GameMoveTicTacToe($player, $fieldKey);
    }

    /**
     * @throws GameMoveException
     */
    private function validateFieldKey(mixed $fieldKey): void
    {
        if (!isset($fieldKey)) {
            throw new GameMoveException(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);
        }
    }
}
