<?php

namespace App\Models\GameCore\Player;

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
