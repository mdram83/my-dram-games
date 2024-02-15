<?php

namespace App\Models\GameCore\Player;

class PlayerAnonymousIdGeneratorMd5 implements PlayerAnonymousIdGenerator
{

    /**
     * @throws PlayerAnonymousIdGeneratorException
     * @return string;
     */
    public function generateId(string $sessionId): string
    {
        if ($sessionId === '') {
            throw new PlayerAnonymousIdGeneratorException(PlayerAnonymousIdGeneratorException::MESSAGE_EMPTY_SESSION_ID);
        }
        return md5($sessionId);
    }
}
