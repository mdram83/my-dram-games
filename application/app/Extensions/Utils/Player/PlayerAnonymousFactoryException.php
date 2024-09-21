<?php

namespace App\Extensions\Utils\Player;

use Exception;

class PlayerAnonymousFactoryException extends Exception
{
    public const MESSAGE_WRONG_ATTRIBUTES = 'Incomplete or incorrect attributes';
}
