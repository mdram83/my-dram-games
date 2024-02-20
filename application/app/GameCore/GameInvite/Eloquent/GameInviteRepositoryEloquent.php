<?php

namespace App\GameCore\GameInvite\Eloquent;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteRepository;
use App\GameCore\GameBox\GameBoxRepository;

class GameInviteRepositoryEloquent implements GameInviteRepository
{

    public function __construct(private readonly GameBoxRepository $gameDefinitionRepository)
    {

    }

    public function getOne(int|string $gameId): GameInvite
    {
        return new GameInviteEloquent($this->gameDefinitionRepository, $gameId);
    }
}
