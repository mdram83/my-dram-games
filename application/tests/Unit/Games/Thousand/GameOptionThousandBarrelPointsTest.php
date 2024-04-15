<?php

namespace Games\Thousand;

use App\GameCore\GameOption\GameOption;
use App\GameCore\GameOption\GameOptionException;
use App\GameCore\GameOptionValue\GameOptionValue;
use App\Games\Thousand\GameOptionThousandBarrelPoints;
use App\Games\Thousand\GameOptionValueThousandBarrelPoints;
use PHPUnit\Framework\TestCase;

class GameOptionThousandBarrelPointsTest extends TestCase
{
    protected array $available = [
        GameOptionValueThousandBarrelPoints::Disabled,
        GameOptionValueThousandBarrelPoints::EightHundred,
    ];
    protected GameOptionValue $default = GameOptionValueThousandBarrelPoints::EightHundred;
    protected GameOptionThousandBarrelPoints $option;

    public function setUp(): void
    {
        parent::setUp();
        $this->option = new GameOptionThousandBarrelPoints($this->available, $this->default);
    }

    public function testInstanceOfGameOption(): void
    {
        $this->assertInstanceOf(GameOption::class, $this->option);
    }

    public function testGetOptionValueClass(): void
    {
        $this->assertEquals(GameOptionValueThousandBarrelPoints::class, $this->option->getOptionValueClass());
    }

    public function testThrowExceptionWhenAvailableValuesWrongType(): void
    {
        $this->expectException(GameOptionException::class);
        $this->expectExceptionMessage(GameOptionException::MESSAGE_INCORRECT_AVAILABLE);
        new GameOptionThousandBarrelPoints(['wrong1', 'wrong2'], $this->default);
    }

    public function testThrowExceptionWhenAvailableValuesAreEmpty(): void
    {
        $this->expectException(GameOptionException::class);
        $this->expectExceptionMessage(GameOptionException::MESSAGE_INCORRECT_AVAILABLE);
        new GameOptionThousandBarrelPoints([], $this->default);
    }
}
