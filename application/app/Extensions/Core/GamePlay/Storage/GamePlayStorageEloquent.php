<?php

namespace App\Extensions\Core\GamePlay\Storage;

use App\Models\GamePlayStorageEloquentModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;
use MyDramGames\Core\Exceptions\GameInviteException;
use MyDramGames\Core\Exceptions\GamePlayStorageException;
use MyDramGames\Core\GameInvite\GameInvite;
use MyDramGames\Core\GameInvite\GameInviteRepository;
use MyDramGames\Core\GamePlay\Storage\GamePlayStorage;

class GamePlayStorageEloquent implements GamePlayStorage
{
    protected Model $model;

    protected GameInviteRepository $gameInviteRepository;
    protected GameInvite $gameInvite;

    /**
     * @throws GamePlayStorageException
     */
    public function __construct(GameInviteRepository $gameInviteRepository, int|string $id = null)
    {
        $this->gameInviteRepository = $gameInviteRepository;

        if ($id === null) {
            $this->registerNewModel();
        } else {
            $this->loadExisingModel($id);
        }
    }

    public function getId(): int|string
    {
        return $this->model->id;
    }

    /**
     * @throws GamePlayStorageException
     */
    public function setGameInvite(GameInvite $invite): void
    {
        try {
            $this->model->gameInviteId = $invite->getId();
            $this->model->save();
            $this->gameInvite = $invite;

        } catch (UniqueConstraintViolationException) {
            throw new GamePlayStorageException(GamePlayStorageException::MESSAGE_INVALID_INVITE);
        }
    }

    /**
     * @throws GamePlayStorageException
     */
    public function getGameInvite(): GameInvite
    {
        if (isset($this->gameInvite)) {
            return $this->gameInvite;
        }

        if (!$gameInviteId = $this->model->gameInviteId) {
            throw new GamePlayStorageException(GamePlayStorageException::MESSAGE_INVITE_NOT_SET);
        }

        try {
            $this->gameInvite = $this->gameInviteRepository->getOne($gameInviteId);
        } catch (GameInviteException) {
            throw new GamePlayStorageException(GamePlayStorageException::MESSAGE_INVALID_INVITE);
        }

        return $this->gameInvite;
    }

    public function setGameData(array $data): void
    {
        $this->model->gameData = json_encode($data);
        $this->model->save();
    }

    public function getGameData(): array
    {
        return json_decode($this->model->gameData, true);
    }

    /**
     * @throws GamePlayStorageException
     */
    public function setSetup(): void
    {
        if ($this->model->setup) {
            throw new GamePlayStorageException(GamePlayStorageException::MESSAGE_SETUP_ALREADY_SET);
        }

        $this->model->setup = true;
        $this->model->save();
    }

    public function getSetup(): bool
    {
        return (bool) $this->model->setup;
    }

    public function setFinished(): void
    {
        $this->model->finished = true;
        $this->model->save();
    }

    public function getFinished(): bool
    {
        return (bool) $this->model->finished;
    }

    private function registerNewModel(): void
    {
        $this->model = new GamePlayStorageEloquentModel();
        $this->model->save();
    }

    /**
     * @throws GamePlayStorageException
     */
    private function loadExisingModel(int|string $id): void
    {
        if (!($model = GamePlayStorageEloquentModel::where('id', $id)->first())) {
            throw new GamePlayStorageException(GamePlayStorageException::MESSAGE_NOT_FOUND);
        }
        $this->model = $model;
    }
}
