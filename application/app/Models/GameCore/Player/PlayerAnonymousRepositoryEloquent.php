<?php

namespace App\Models\GameCore\Player;

class PlayerAnonymousRepositoryEloquent implements PlayerAnonymousRepository
{
    /**
     * @throws PlayerAnonymousRepositoryException
     */
    public function getOne(string $hash): PlayerAnonymous
    {
        if (!$player = PlayerAnonymousEloquent::where('hash', $hash)->first()) {
            throw new PlayerAnonymousRepositoryException(PlayerAnonymousRepositoryException::MESSAGE_NOT_FOUND);
        }
        return $player;
    }
}
