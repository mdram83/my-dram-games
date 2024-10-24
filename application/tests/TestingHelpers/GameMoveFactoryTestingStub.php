<?php

namespace Tests\TestingHelpers;

use MyDramGames\Core\GameMove\GameMove;
use MyDramGames\Core\GameMove\GameMoveFactory;
use MyDramGames\Utils\Player\Player;

class GameMoveFactoryTestingStub implements GameMoveFactory
{
    /**
     * @inheritDoc
     */
    public static function create(Player $player, array $inputs): GameMove
    {
        return new class($player, $inputs) implements GameMove
        {
            public function __construct(protected Player $player, protected array $inputs)
            {

            }

            public function getPlayer(): Player
            {
                return $this->player;
            }

            public function getDetails(): array
            {
                return $this->inputs;
            }
        };
    }
}
