<?php

namespace App\Broadcasting\GameCore\Game;

use App\Models\GameCore\Game\GameRepository;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Support\Facades\App;

class GameChannel extends Channel
{
    private GameRepository $repository;
    public const CHANNEL_NAME = 'game.{gameId}';

    public function __construct()
    {
        parent::__construct(static::CHANNEL_NAME);
        $this->repository = App::make(GameRepository::class);
    }

    public function join(User $user, int|string $gameId): array|bool
    {
        if ($this->repository->getOne($gameId)->isPlayerAdded($user)) {
            return ['name' => $user->getName()];
        }
        return false;
    }
}
