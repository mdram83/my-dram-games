<?php

namespace Tests\Unit\GameCore\Services\HashGenerator\Md5;

use App\GameCore\Services\HashGenerator\HashGeneratorException;
use App\GameCore\Services\HashGenerator\Md5\HashGeneratorMd5;
use PHPUnit\Framework\TestCase;

class HashGeneratorMd5Test extends TestCase
{
    public function testEmptyStringResultInException(): void
    {
        $this->expectException(HashGeneratorException::class);
        $generator = new HashGeneratorMd5();
        $key = '';
        $generator->generateHash($key);
    }

    public function testReturnMd5OfProvidedValue(): void
    {
        $generator = new HashGeneratorMd5();
        $key = 'test-key-id12345';
        $anonymousPlayerId = $generator->generateHash($key);

        $this->assertEquals(md5($key), $anonymousPlayerId);
    }
}
