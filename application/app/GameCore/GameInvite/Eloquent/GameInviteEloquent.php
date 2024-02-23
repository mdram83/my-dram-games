<?php

namespace App\GameCore\GameInvite\Eloquent;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteException;
use App\GameCore\GameBox\GameBox;
use App\GameCore\GameBox\GameBoxRepository;
use App\GameCore\Player\Player;
use App\GameCore\Player\PlayerAnonymous;
use App\GameCore\Player\PlayerRegistered;
use App\Models\GameInviteEloquentModel;

class GameInviteEloquent implements GameInvite
{
    protected GameInviteEloquentModel $model;
    protected GameBoxRepository $gameBoxRepository;
    protected GameBox $gameBox;

    public function __construct(GameBoxRepository $gameBoxRepository, string $id = null)
    {
        $this->gameBoxRepository = $gameBoxRepository;

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

    /**
     * @throws GameInviteException
     */
    public function addPlayer(Player $player, bool $host = false): void
    {
        if (!$this->hasNumberOfPlayers()) {
            throw new GameInviteException(GameInviteException::MESSAGE_NO_OF_PLAYERS_NOT_SET);
        }

        if ($this->isPlayerAdded($player)) {
            throw new GameInviteException(GameInviteException::MESSAGE_PLAYER_ALREADY_ADDED);
        }

        if (!$this->canAddMorePlayers()) {
            throw new GameInviteException(GameInviteException::MESSAGE_TOO_MANY_PLAYERS);
        }

        if ($host === true && $this->hasHost()) {
            throw new GameInviteException(GameInviteException::MESSAGE_HOST_ALREADY_ADDED);
        }

        if ($host === false && !$this->hasPlayers()) {
            throw new GameInviteException(GameInviteException::MESSAGE_HOST_NOT_SET);
        }

        if ($host === true) {
            $this->model->hostable()->associate($player);
            $this->saveModel();
        }

        if ($player->isRegistered() === true) {
            $this->model->playersRegistered()->attach($player->getId());

        } elseif ($player->isRegistered() === false) {
            $this->model->playersAnonymous()->attach($player->getId());

        } else {
            throw new GameInviteException(GameInviteException::MESSAGE_PLAYER_TYPE_UNSET);
        }

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
            throw new GameInviteException(GameInviteException::MESSAGE_HOST_NOT_SET);
        }

        return $this->model->hostable;
    }

    public function isHost(Player $player): bool
    {
        return $this->getHost()->getId() === $player->getId();
    }

    public function setNumberOfPlayers(int $numberOfPlayers): void
    {
        if (!$this->hasGameBox()) {
            throw new GameInviteException(GameInviteException::MESSAGE_GAME_BOX_NOT_SET);
        }

        if (!$this->isAllowedNumberOfPlayers($numberOfPlayers)) {
            throw new GameInviteException(GameInviteException::MESSAGE_NO_OF_PLAYERS_EXCEED_DEF);
        }

        if ($this->hasNumberOfPlayers()) {
            throw new GameInviteException(GameInviteException::MESSAGE_NO_OF_PLAYERS_WAS_SET);
        }

        $this->model->numberOfPlayers = $numberOfPlayers;
        $this->saveModel();
    }

    public function getNumberOfPlayers(): int
    {
        if (!$this->hasNumberOfPlayers()) {
            throw new GameInviteException(GameInviteException::MESSAGE_NO_OF_PLAYERS_NOT_SET);
        }
        return $this->model->numberOfPlayers;
    }

    public function setGameBox(GameBox $gameBox): void
    {
        if ($this->hasGameBox()) {
            throw new GameInviteException(GameInviteException::MESSAGE_GAME_BOX_ALREADY_SET);
        }

        $this->gameBox = $gameBox;
        $this->model->gameBox = $gameBox->getSlug();
        $this->saveModel();
    }

    public function getGameBox(): GameBox
    {
        if (!$this->hasGameBox()) {
            throw new GameInviteException(GameInviteException::MESSAGE_GAME_BOX_NOT_SET);
        }

        if (!isset($this->gameBox)) {
            $slug = $this->model->gameBox;
            $this->gameBox = $this->gameBoxRepository->getOne($slug);
        }

        return $this->gameBox;
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

    protected function hasGameBox(): bool
    {
        return isset($this->model->gameBox);
    }

    protected function isAllowedNumberOfPlayers(int $numberOfPlayers): bool
    {
        return in_array($numberOfPlayers, $this->getGameBox()->getGameSetup()->getNumberOfPlayers());
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
        $this->model = new GameInviteEloquentModel();
        $this->saveModel();
    }

    protected function loadExisingModel(string $id): void
    {
        if (!($model = GameInviteEloquentModel::where('id', $id)->first())) {
            throw new GameInviteException(GameInviteException::MESSAGE_GAME_NOT_FOUND);
        }
        $this->model = $model;
    }
}
