<?php

namespace Tests\Unit\Model\GameCore\Player;

use App\Models\GameCore\Player\PlayerAnonymousIdGeneratorException;
use App\Models\GameCore\Player\PlayerAnonymousIdGeneratorMd5;
use PHPUnit\Framework\TestCase;

class PlayerAnonymousIdGeneratorMd5Test extends TestCase
{
    public function testEmptyStringResultInException(): void
    {
        $this->expectException(PlayerAnonymousIdGeneratorException::class);
        $generator = new PlayerAnonymousIdGeneratorMd5();
        $sessionId = '';
        $generator->generateId($sessionId);
    }

    public function testReturnMd5OfProvidedValue(): void
    {
        $generator = new PlayerAnonymousIdGeneratorMd5();
        $sessionId = 'test-session-id';
        $anonymousPlayerId = $generator->generateId($sessionId);

        $this->assertEquals(md5($sessionId), $anonymousPlayerId);
    }
}
