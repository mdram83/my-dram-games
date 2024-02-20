<?php

namespace App\GameCore\Services\HashGenerator\Md5;

use App\GameCore\Services\HashGenerator\PlayerAnonymousHashGenerator;
use App\GameCore\Services\HashGenerator\PlayerAnonymousHashGeneratorException;

class PlayerAnonymousHashGeneratorMd5 implements PlayerAnonymousHashGenerator
{

    /**
     * @return string;
     * @throws PlayerAnonymousHashGeneratorException
     */
    public function generateHash(string $sessionId): string
    {
        if ($sessionId === '') {
            throw new PlayerAnonymousHashGeneratorException(PlayerAnonymousHashGeneratorException::MESSAGE_EMPTY_SESSION_ID);
        }
        return md5($sessionId);
    }
}
