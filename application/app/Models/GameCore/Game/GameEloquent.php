<?php

namespace App\Models\GameCore\Game;

use App\Models\GameCore\GameDefinition\GameDefinition;
use App\Models\GameCore\GameDefinition\GameDefinitionFactory;
use App\Models\GameCore\Player\Player;

class GameEloquent implements Game
{
    protected GameEloquentModel $model;
    protected GameDefinitionFactory $gameDefinitionFactory;
    protected GameDefinition $gameDefinition;

    public function __construct(GameDefinitionFactory $gameDefinitionFactory, string $id = null)
    {
        $this->gameDefinitionFactory = $gameDefinitionFactory;

        if ($id === null) {
            $this->registerNewModel();
        } else {
            $this->loadExisingModel($id);
        }

    }

    public function getId(): string|int
    {
        return $this->model->id;
    }

    public function addPlayer(Player $player, bool $host = false): void
    {
        if (!$this->hasNumberOfPlayers()) {
            throw new GameException('Number of players not set');
        }

        if ($this->isPlayerAdded($player)) {
            throw new GameException('Player already added');
        }

        if (!$this->canAddMorePlayers()) {
            throw new GameException('Number of players exceeded');
        }

        if ($host === true && $this->hasHost()) {
            throw new GameException('Host already added');
        }

        if ($host === false && !$this->hasPlayers()) {
            throw new GameException('Host not set');
        }

        if ($host === true) {
            $this->model->host()->associate($player);
            $this->saveModel();
        }

        $this->model->players()->attach($player->getId());
        $this->model->refresh();
    }

    public function getPlayers(): array
    {
        return $this->model->players->all();
    }

    public function getHost(): Player
    {
        if (!$this->hasHost()) {
            throw new GameException('Host not set');
        }

        return $this->model->host;
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
        $this->saveModel();
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

        $this->model->gameDefinition = $gameDefinition->getSlug();
        $this->saveModel();
    }

    public function getGameDefinition(): GameDefinition
    {
        if (!$this->hasGameDefinition()) {
            throw new GameException('Game definition not set');
        }

        if (!isset($this->gameDefinition)) {
            $slug = $this->model->gameDefinition;
            $this->gameDefinition = $this->gameDefinitionFactory->create($slug);
        }

        return $this->gameDefinition;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'host' => ['name' => $this->getHost()->getName()],
            'numberOfPlayers' => $this->getNumberOfPlayers(),
            'players' => array_map(fn($player) => ['name' => $player->getName()], $this->getPlayers()),
        ];
    }

    protected function hasNumberOfPlayers(): bool
    {
        return isset($this->model->numberOfPlayers);
    }

    protected function canAddMorePlayers(): bool
    {
        return count($this->getPlayers()) < $this->getNumberOfPlayers();
    }

    public function isPlayerAdded(Player $player): bool
    {
        return in_array(
            $player->getId(),
            array_map(fn($currentPlayer) => $currentPlayer->getId(), $this->getPlayers())
        );
    }

    protected function hasHost(): bool
    {
        return isset($this->model->host);
    }

    protected function hasGameDefinition(): bool
    {
        return isset($this->model->gameDefinition);
    }

    protected function isAllowedNumberOfPlayers(int $numberOfPlayers): bool
    {
        return in_array($numberOfPlayers, $this->getGameDefinition()->getNumberOfPlayers());
    }

    protected function saveModel(): void
    {
        $this->model->save();
    }

    protected function hasPlayers(): bool
    {
        return count($this->getPlayers()) > 0;
    }

    protected function registerNewModel(): void
    {
        $this->model = new GameEloquentModel();
        $this->saveModel();
    }

    protected function loadExisingModel(string $id): void
    {
        if (!($model = GameEloquentModel::where('id', $id)->first())) {
            throw new GameException('Game not found');
        }
        $this->model = $model;
    }
}
