<?php

namespace App\GameCore\Services\HashGenerator;

use Exception;

class HashGeneratorException extends Exception
{
    public const MESSAGE_EMPTY_KEY = 'Key not provided for hashing';
}
