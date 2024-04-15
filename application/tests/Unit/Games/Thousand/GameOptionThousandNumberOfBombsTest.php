<?php

namespace Games\Thousand;

use App\GameCore\GameOption\GameOption;
use App\GameCore\GameOption\GameOptionException;
use App\GameCore\GameOptionValue\GameOptionValue;
use App\Games\Thousand\GameOptionThousandBarrelPoints;
use App\Games\Thousand\GameOptionThousandNumberOfBombs;
use App\Games\Thousand\GameOptionValueThousandBarrelPoints;
use App\Games\Thousand\GameOptionValueThousandNumberOfBombs;
use PHPUnit\Framework\TestCase;

class GameOptionThousandNumberOfBombsTest extends TestCase
{
    protected array $available = [
        GameOptionValueThousandNumberOfBombs::Disabled,
        GameOptionValueThousandNumberOfBombs::One,
    ];
    protected GameOptionValue $default = GameOptionValueThousandNumberOfBombs::Disabled;
    protected GameOptionThousandNumberOfBombs $option;

    public function setUp(): void
    {
        parent::setUp();
        $this->option = new GameOptionThousandNumberOfBombs($this->available, $this->default);
    }

    public function testInstanceOfGameOption(): void
    {
        $this->assertInstanceOf(GameOption::class, $this->option);
    }

    public function testGetOptionValueClass(): void
    {
        $this->assertEquals(GameOptionValueThousandNumberOfBombs::class, $this->option->getOptionValueClass());
    }

    public function testThrowExceptionWhenAvailableValuesWrongType(): void
    {
        $this->expectException(GameOptionException::class);
        $this->expectExceptionMessage(GameOptionException::MESSAGE_INCORRECT_AVAILABLE);
        new GameOptionThousandNumberOfBombs(['wrong1', 'wrong2'], $this->default);
    }

    public function testThrowExceptionWhenAvailableValuesAreEmpty(): void
    {
        $this->expectException(GameOptionException::class);
        $this->expectExceptionMessage(GameOptionException::MESSAGE_INCORRECT_AVAILABLE);
        new GameOptionThousandNumberOfBombs([], $this->default);
    }
}
