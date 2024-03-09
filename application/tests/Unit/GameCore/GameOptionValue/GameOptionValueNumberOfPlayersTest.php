<?php

namespace Tests\Unit\GameCore\GameOptionValue;

use App\GameCore\GameOptionValue\GameOptionValue;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use PHPUnit\Framework\TestCase;

class GameOptionValueNumberOfPlayersTest extends TestCase
{
    public function testInstanceOfGameOptions(): void
    {
        $reflection = new \ReflectionClass(GameOptionValueNumberOfPlayers::class);
        $this->assertTrue($reflection->implementsInterface(GameOptionValue::class));
    }
}
