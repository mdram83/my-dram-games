<?php

namespace App\Models\GameCore\Player;

class PlayerAnonymousRepositoryEloquent implements PlayerAnonymousRepository
{
    /**
     * @throws PlayerAnonymousRepositoryException
     */
    public function getOne(string $hash): ?PlayerAnonymous
    {
        if ($hash === '') {
            throw new PlayerAnonymousRepositoryException(PlayerAnonymousRepositoryException::MESSAGE_MISSING_HASH);
        }
        return PlayerAnonymousEloquent::where('hash', $hash)->first();
    }
}
