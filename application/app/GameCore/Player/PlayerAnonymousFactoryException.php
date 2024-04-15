<?php

namespace App\GameCore\Player;

use Exception;

class PlayerAnonymousFactoryException extends Exception
{
    public const MESSAGE_WRONG_ATTRIBUTES = 'Incomplete or incorrect attributes';
}
