<?php

namespace App\GameCore\Services\HashGenerator;

class HashGeneratorException extends \Exception
{
    public const MESSAGE_EMPTY_KEY = 'Key not provided for hashing';
}
