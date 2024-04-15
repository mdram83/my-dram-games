<?php

namespace Tests\Unit\GameCore\GameOptionType;

use App\GameCore\GameOptionType\GameOptionType;
use App\GameCore\GameOptionType\GameOptionTypeEnum;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class GameOptionTypeEnumTest extends TestCase
{
    public function testInstanceOfGameOptionType(): void
    {
        $reflection = new ReflectionClass(GameOptionTypeEnum::class);
        $this->assertTrue($reflection->implementsInterface(GameOptionType::class));
    }
}
