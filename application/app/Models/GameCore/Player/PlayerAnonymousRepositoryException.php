<?php

namespace App\Models\GameCore\Player;

class PlayerAnonymousRepositoryException extends \Exception
{
    public const MESSAGE_MISSING_HASH = 'Hash not provided';
}
