<?php

namespace App\GameCore\Player\Eloquent;

use App\GameCore\Player\PlayerAnonymous;
use App\GameCore\Player\PlayerAnonymousRepository;
use App\GameCore\Player\PlayerAnonymousRepositoryException;
use App\Models\PlayerAnonymousEloquent;

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
