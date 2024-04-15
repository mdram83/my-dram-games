<?php

namespace Games\Thousand;

use App\GameCore\GameOption\GameOption;
use App\GameCore\GameOption\GameOptionException;
use App\GameCore\GameOptionValue\GameOptionValue;
use App\Games\Thousand\GameOptionThousandReDealConditions;
use App\Games\Thousand\GameOptionValueThousandReDealConditions;
use PHPUnit\Framework\TestCase;

class GameOptionThousandReDealConditionsTest extends TestCase
{
    protected array $available = [
        GameOptionValueThousandReDealConditions::Disabled,
        GameOptionValueThousandReDealConditions::FourNines,
    ];
    protected GameOptionValue $default = GameOptionValueThousandReDealConditions::Disabled;
    protected GameOptionThousandReDealConditions $option;

    public function setUp(): void
    {
        parent::setUp();
        $this->option = new GameOptionThousandReDealConditions($this->available, $this->default);
    }

    public function testInstanceOfGameOption(): void
    {
        $this->assertInstanceOf(GameOption::class, $this->option);
    }

    public function testGetOptionValueClass(): void
    {
        $this->assertEquals(GameOptionValueThousandReDealConditions::class, $this->option->getOptionValueClass());
    }

    public function testThrowExceptionWhenAvailableValuesWrongType(): void
    {
        $this->expectException(GameOptionException::class);
        $this->expectExceptionMessage(GameOptionException::MESSAGE_INCORRECT_AVAILABLE);
        new GameOptionThousandReDealConditions(['wrong1', 'wrong2'], $this->default);
    }

    public function testThrowExceptionWhenAvailableValuesAreEmpty(): void
    {
        $this->expectException(GameOptionException::class);
        $this->expectExceptionMessage(GameOptionException::MESSAGE_INCORRECT_AVAILABLE);
        new GameOptionThousandReDealConditions([], $this->default);
    }
}
