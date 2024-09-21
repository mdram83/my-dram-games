<?php

namespace App\Services\HashGenerator\Md5;

use App\Services\HashGenerator\HashGenerator;
use App\Services\HashGenerator\HashGeneratorException;

class HashGeneratorMd5 implements HashGenerator
{

    /**
     * @return string;
     * @throws HashGeneratorException
     */
    public function generateHash(string $key): string
    {
        if ($key === '') {
            throw new HashGeneratorException(HashGeneratorException::MESSAGE_EMPTY_KEY);
        }
        return md5($key);
    }
}
