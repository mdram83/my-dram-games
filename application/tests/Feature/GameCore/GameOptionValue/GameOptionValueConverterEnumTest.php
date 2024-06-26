<?php

namespace Tests\Feature\GameCore\GameOptionValue;

use App\GameCore\GameOptionValue\GameOptionValue;
use App\GameCore\GameOptionValue\GameOptionValueConverter;
use App\GameCore\GameOptionValue\GameOptionValueException;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class GameOptionValueConverterEnumTest extends TestCase
{
    protected GameOptionValueConverter $converter;

    public function setUp(): void
    {
        parent::setUp();
        $this->converter = App::make(GameOptionValueConverter::class);
    }

    public function testInstanceOfGameOptionValueConverter(): void
    {
        $this->assertInstanceOf(GameOptionValueConverter::class, $this->converter);
    }

    public function testThrowExceptionWhenValueIsMissing(): void
    {
        $this->expectException(GameOptionValueException::class);
        $this->expectExceptionMessage(GameOptionValueException::MESSAGE_MISSING_VALUE);

        $this->converter->convert(2, 'autostart');
    }

    public function testConvert(): void
    {
        $this->assertInstanceOf(GameOptionValue::class, $this->converter->convert('0', 'autostart'));
        $this->assertInstanceOf(GameOptionValue::class, $this->converter->convert('1', 'autostart'));
        $this->assertInstanceOf(GameOptionValue::class, $this->converter->convert( 0, 'autostart'));
        $this->assertInstanceOf(GameOptionValue::class, $this->converter->convert( 1, 'autostart'));
        $this->assertInstanceOf(GameOptionValue::class, $this->converter->convert(false, 'autostart'));
        $this->assertInstanceOf(GameOptionValue::class, $this->converter->convert(true, 'autostart'));
    }
}
