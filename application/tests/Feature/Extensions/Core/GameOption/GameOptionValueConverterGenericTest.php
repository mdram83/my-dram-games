<?php

namespace Tests\Feature\Extensions\Core\GameOption;

use App\Extensions\Core\GameOption\GameOptionValueConverter;
use Illuminate\Support\Facades\App;
use MyDramGames\Core\Exceptions\GameOptionValueException;
use MyDramGames\Core\GameOption\GameOptionValue;
use Tests\TestCase;

class GameOptionValueConverterGenericTest extends TestCase
{
    protected GameOptionValueConverter $converter;

    public function setUp(): void
    {
        parent::setUp();
        $this->converter = App::make(\App\Extensions\Core\GameOption\GameOptionValueConverter::class);
    }

    public function testInstanceOfGameOptionValueConverter(): void
    {
        $this->assertInstanceOf(\App\Extensions\Core\GameOption\GameOptionValueConverter::class, $this->converter);
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
