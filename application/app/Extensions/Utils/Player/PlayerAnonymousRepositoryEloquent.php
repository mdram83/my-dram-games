<?php

namespace App\Extensions\Utils\Player;

use App\Models\PlayerAnonymousEloquent;
use MyDramGames\Utils\Player\PlayerAnonymous;

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
