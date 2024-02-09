<?php

namespace App\Broadcasting\GameCore;

use App\Models\GameCore\Game\GameRepository;
use App\Models\User;

class GameChannel
{
    public function __construct(private readonly GameRepository $repository)
    {

    }

    public function join(User $user, int|string $gameId): array|bool
    {
        if ($this->repository->getOne($gameId)->isPlayerAdded($user)) {
            return ['name' => $user->getName()];
        }
        return false;
    }
}
