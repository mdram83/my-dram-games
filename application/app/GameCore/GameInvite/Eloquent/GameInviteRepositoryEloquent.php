<?php

namespace App\GameCore\GameInvite\Eloquent;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteException;
use App\GameCore\GameInvite\GameInviteRepository;
use App\GameCore\GameBox\GameBoxRepository;
use App\GameCore\Services\Collection\Collection;

class GameInviteRepositoryEloquent implements GameInviteRepository
{

    public function __construct(
        private readonly GameBoxRepository $gameBoxRepository,
        private readonly Collection $optionsHandler,
    )
    {

    }

    /**
     * @throws GameInviteException
     */
    public function getOne(int|string $gameId): GameInvite
    {
        return new GameInviteEloquent($this->gameBoxRepository, $this->optionsHandler, $gameId);
    }
}
