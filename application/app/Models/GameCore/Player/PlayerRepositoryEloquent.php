<?php

namespace App\Models\GameCore\Player;


use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PlayerRepositoryEloquent implements PlayerRepository
{
    public function __construct(protected PlayerAnonymousHashGenerator $generator)
    {

    }

    public function getOneCurrent(): Player
    {
        if (Auth::check()) {
            return User::find(Auth::id());
        }

        $hash = $this->generator->generateHash(session()->getId());

        return
            PlayerAnonymousEloquent::where(['hash' => $hash])->first()
            ?? PlayerAnonymousEloquent::factory()->create(['hash' => $hash]);
    }
}
