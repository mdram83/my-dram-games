<?php

namespace App\Models\GameCore\Player;

class PlayerAnonymousIdGeneratorException extends \Exception
{
    public const MESSAGE_EMPTY_SESSION_ID = 'Can not generate id for anonymous player';
}
