<?php

namespace Tests\Unit\Model;

use App\Models\GameCore\Player\Player;
use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testIsInstanceOfPlayer(): void
    {
        $mockUser = $this->createMock(User::class);
        $this->assertInstanceOf(Player::class, $mockUser);
    }

    public function testGetId(): void
    {
        $userId = 1;
        $mockUser = $this->createMock(User::class);
        $mockUser->method('getId')->willReturn($userId);

        $this->assertSame($userId, $mockUser->getId());
    }

    public function testGetName(): void
    {
        $userName = 'JohnDoe';
        $mockUser = $this->createMock(User::class);
        $mockUser->method('getName')->willReturn($userName);

        $this->assertSame($userName, $mockUser->getName());
    }
}
