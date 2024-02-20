<?php

namespace Tests\Unit\GameCore\Services\HashGenerator\Md5;

use App\GameCore\Services\HashGenerator\Md5\HashGeneratorMd5;
use PHPUnit\Framework\TestCase;

class HashGeneratorMd5Test extends TestCase
{
    public function testEmptyStringResultInException(): void
    {
        $this->expectException(\App\GameCore\Services\HashGenerator\HashGeneratorException::class);
        $generator = new HashGeneratorMd5();
        $sessionId = '';
        $generator->generateHash($sessionId);
    }

    public function testReturnMd5OfProvidedValue(): void
    {
        $generator = new \App\GameCore\Services\HashGenerator\Md5\HashGeneratorMd5();
        $sessionId = 'test-session-id';
        $anonymousPlayerId = $generator->generateHash($sessionId);

        $this->assertEquals(md5($sessionId), $anonymousPlayerId);
    }
}
