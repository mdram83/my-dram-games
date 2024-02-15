<?php

namespace App\Models\GameCore\Player;


use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PlayerRepositoryEloquent implements PlayerRepository
{
    public function __construct(protected PlayerAnonymousIdGenerator $generator)
    {

    }

    public function getOneCurrent(): Player
    {
        if (Auth::check()) {
            return User::find(Auth::id());
        }

        $playerId = $this->generator->generateId(session()->getId());
        return
            PlayerAnonymousEloquent::find($playerId)
            ?? PlayerAnonymousEloquent::factory()->create(['id' => $playerId]);
    }
}
