<?php

namespace App\Models\GameCore\Game;

use App\Models\GameCore\GameDefinition\GameDefinition;
use App\Models\GameCore\Player\Player;

class GameEloquent implements Game
{
    protected GameEloquentModel $model;
    protected GameDefinition $gameDefinition;
    protected array $players = [];
    protected Player $host;

    public function __construct()
    {
        $this->model = new GameEloquentModel();
    }

    public function addPlayer(Player $player, bool $host = false): void
    {
        if (!$this->hasNumberOfPlayers()) {
            throw new GameException('Number of players not set');
        }

        if (!$this->canAddMorePlayers()) {
            throw new GameException('Number of players exceeded');
        }

        if ($this->isPlayerAdded($player)) {
            throw new GameException('Player already added');
        }

        if ($host === true && $this->hasHost()) {
            throw new GameException('Host already added');
        }

        if ($host === false && count($this->players) === 0) {
            throw new GameException('Host not set');
        }

        $this->players[] = $player;

        if ($host === true) {
            $this->host = $player;
//            $this->model->host()->associate($player); // TODO seems I need to save model before this step. Think how to solve that for unit testing (if possible).
        }

        // TODO: Implement addPlayer() method (saving in model as relationship / sync, attach etc.)
        // TODO: as above for host
    }

    public function getPlayers(): array
    {
        return $this->players;
    }

    public function getHost(): Player
    {
        if (!$this->hasHost()) {
            throw new GameException('Host not set');
        }
        return $this->host;
    }

    public function setNumberOfPlayers(int $numberOfPlayers): void
    {
        if (!$this->hasGameDefinition()) {
            throw new GameException('Game definition not set');
        }

        if (!$this->isAllowedNumberOfPlayers($numberOfPlayers)) {
            throw new GameException('Number of players not matching game definition');
        }

        if ($this->hasNumberOfPlayers()) {
            throw new GameException('Number of players already set');
        }

        $this->model->numberOfPlayers = $numberOfPlayers;
    }

    public function getNumberOfPlayers(): int
    {
        if (!$this->hasNumberOfPlayers()) {
            throw new GameException('Number of players not set');
        }
        return $this->model->numberOfPlayers;
    }

    public function setGameDefinition(GameDefinition $gameDefinition): void
    {
        if ($this->hasGameDefinition()) {
            throw new GameException('Game definition already set');
        }
        $this->gameDefinition = $gameDefinition;

        // TODO: Implement setGameDefinition() method (saving in database with model property using e.g. slug only or seralized object)
    }

    public function getGameDefinition(): GameDefinition
    {
        if (!$this->hasGameDefinition()) {
            throw new GameException('Game definition not set');
        }
        return $this->gameDefinition;
    }

    protected function hasNumberOfPlayers(): bool
    {
        return isset($this->model->numberOfPlayers);
    }

    protected function canAddMorePlayers(): bool
    {
        return count($this->players) < $this->getNumberOfPlayers();
    }

    protected function isPlayerAdded(Player $player): bool
    {
        return in_array(
            $player->getId(),
            array_map(fn($currentPlayer) => $currentPlayer->getId(), $this->players)
        );
    }

    protected function hasHost(): bool
    {
        return isset($this->host);
    }

    protected function hasGameDefinition(): bool
    {
        return isset($this->gameDefinition);
    }

    protected function isAllowedNumberOfPlayers(int $numberOfPlayers): bool
    {
        return in_array($numberOfPlayers, $this->gameDefinition->getNumberOfPlayers());
    }
}
