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
            throw new GameException(GameException::MESSAGE_NO_OF_PLAYERS_NOT_SET);
        }

        if ($this->isPlayerAdded($player)) {
            throw new GameException(GameException::MESSAGE_PLAYER_ALREADY_ADDED);
        }

        if (!$this->canAddMorePlayers()) {
            throw new GameException(GameException::MESSAGE_TOO_MANY_PLAYERS);
        }

        if ($host === true && $this->hasHost()) {
            throw new GameException(GameException::MESSAGE_HOST_ALREADY_ADDED);
        }

        if ($host === false && !$this->hasPlayers()) {
            throw new GameException(GameException::MESSAGE_HOST_NOT_SET);
        }

        if ($host === true) {
            $this->model->hostable()->associate($player);
            $this->saveModel();
        }

        $this->model->playersRegistered()->attach($player->getId());
        $this->model->refresh();
    }

    public function getPlayers(): array
    {
        return array_merge(
            $this->model->playersRegistered->all(),
            $this->model->playersAnonymous->all()
        );
    }

    public function getHost(): Player
    {
        if (!$this->hasHost()) {
            throw new GameException(GameException::MESSAGE_HOST_NOT_SET);
        }

        return $this->model->hostable;
    }

    public function isHost(Player $player): bool
    {
        return $this->getHost()->getId() === $player->getId();
    }

    public function setNumberOfPlayers(int $numberOfPlayers): void
    {
        if (!$this->hasGameDefinition()) {
            throw new GameException(GameException::MESSAGE_GAME_DEFINITION_NOT_SET);
        }

        if (!$this->isAllowedNumberOfPlayers($numberOfPlayers)) {
            throw new GameException(GameException::MESSAGE_NO_OF_PLAYERS_EXCEED_DEF);
        }

        if ($this->hasNumberOfPlayers()) {
            throw new GameException(GameException::MESSAGE_NO_OF_PLAYERS_WAS_SET);
        }

        $this->model->numberOfPlayers = $numberOfPlayers;
        $this->saveModel();
    }

    public function getNumberOfPlayers(): int
    {
        if (!$this->hasNumberOfPlayers()) {
            throw new GameException(GameException::MESSAGE_NO_OF_PLAYERS_NOT_SET);
        }
        return $this->model->numberOfPlayers;
    }

    public function setGameDefinition(GameDefinition $gameDefinition): void
    {
        if ($this->hasGameDefinition()) {
            throw new GameException(GameException::MESSAGE_GAME_DEFINITION_WAS_SET);
        }

        $this->gameDefinition = $gameDefinition;

        $this->model->gameDefinition = $gameDefinition->getSlug();
        $this->saveModel();
    }

    public function getGameDefinition(): GameDefinition
    {
        if (!$this->hasGameDefinition()) {
            throw new GameException(GameException::MESSAGE_GAME_DEFINITION_NOT_SET);
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
        return isset($this->model->hostable);
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
            throw new GameException(GameException::MESSAGE_GAME_NOT_FOUND);
        }
        $this->model = $model;
    }
}
