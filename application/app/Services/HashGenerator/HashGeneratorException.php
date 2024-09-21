<?php

namespace App\Services\HashGenerator;

use Exception;

class HashGeneratorException extends Exception
{
    public const string MESSAGE_EMPTY_KEY = 'Key not provided for hashing';
}
