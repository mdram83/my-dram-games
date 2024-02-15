<?php

namespace Tests\Unit\Model\GameCore\Player;

use App\Models\GameCore\Player\PlayerAnonymousHashGeneratorException;
use App\Models\GameCore\Player\PlayerAnonymousHashGeneratorMd5;
use PHPUnit\Framework\TestCase;

class PlayerAnonymousHashGeneratorMd5Test extends TestCase
{
    public function testEmptyStringResultInException(): void
    {
        $this->expectException(PlayerAnonymousHashGeneratorException::class);
        $generator = new PlayerAnonymousHashGeneratorMd5();
        $sessionId = '';
        $generator->generateHash($sessionId);
    }

    public function testReturnMd5OfProvidedValue(): void
    {
        $generator = new PlayerAnonymousHashGeneratorMd5();
        $sessionId = 'test-session-id';
        $anonymousPlayerId = $generator->generateHash($sessionId);

        $this->assertEquals(md5($sessionId), $anonymousPlayerId);
    }
}
