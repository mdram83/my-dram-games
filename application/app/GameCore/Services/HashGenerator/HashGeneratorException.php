<?php

namespace App\GameCore\Services\HashGenerator;

class HashGeneratorException extends \Exception
{
    public const MESSAGE_EMPTY_SESSION_ID = 'Can not generate id for anonymous player';
}
