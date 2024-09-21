<?php

namespace App\Extensions\Utils\Player;

use Exception;

class PlayerAnonymousRepositoryException extends Exception
{
    public const string MESSAGE_MISSING_HASH = 'Hash not provided';
}
