<?php

namespace App\Games\TicTacToe\Elements;

use App\GameCore\GameElements\GameCharacter\GameCharacter;
use App\GameCore\GameElements\GameCharacter\GameCharacterException;
use App\GameCore\Player\Player;

class GameCharacterTicTacToe implements GameCharacter
{
    /**
     * @throws GameCharacterException
     */
    public function __construct(
        private readonly string $name,
        private readonly Player $player
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
