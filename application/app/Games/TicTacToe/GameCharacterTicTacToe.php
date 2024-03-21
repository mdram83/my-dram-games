<?php

namespace App\Games\TicTacToe;

use App\GameCore\GamePlay\GameCharacterException;
use App\GameCore\Player\Player;

class GameCharacterTicTacToe implements \App\GameCore\GamePlay\GameCharacter
{
    /**
     * @throws GameCharacterException
     */
    public function __construct(
        private readonly string $name,
        private Player $player
    )
    {
        if (!in_array($this->name, ['x', 'o'])) {
            throw new GameCharacterException(GameCharacterException::MESSAGE_WRONG_NAME);
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }
}
