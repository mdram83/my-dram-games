<?php

namespace Tests\Unit\GameCore\Services\HashGenerator\Md5;

use App\GameCore\Services\HashGenerator\Md5\PlayerAnonymousHashGeneratorMd5;
use PHPUnit\Framework\TestCase;

class PlayerAnonymousHashGeneratorMd5Test extends TestCase
{
    public function testEmptyStringResultInException(): void
    {
        $this->expectException(\App\GameCore\Services\HashGenerator\PlayerAnonymousHashGeneratorException::class);
        $generator = new PlayerAnonymousHashGeneratorMd5();
        $sessionId = '';
        $generator->generateHash($sessionId);
    }

    public function testReturnMd5OfProvidedValue(): void
    {
        $generator = new \App\GameCore\Services\HashGenerator\Md5\PlayerAnonymousHashGeneratorMd5();
        $sessionId = 'test-session-id';
        $anonymousPlayerId = $generator->generateHash($sessionId);

        $this->assertEquals(md5($sessionId), $anonymousPlayerId);
    }
}
