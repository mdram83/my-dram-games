<?php

namespace App\GameCore\Player;

class PlayerAnonymousRepositoryException extends \Exception
{
    public const MESSAGE_MISSING_HASH = 'Hash not provided';
}
