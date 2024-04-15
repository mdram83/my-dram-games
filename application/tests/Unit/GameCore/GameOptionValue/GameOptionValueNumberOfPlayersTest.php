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

    public function testGetValue(): void
    {
        $players2 = GameOptionValueNumberOfPlayers::Players002;
        $players3 = GameOptionValueNumberOfPlayers::Players003;
        $players4 = GameOptionValueNumberOfPlayers::Players004;
        $players5 = GameOptionValueNumberOfPlayers::Players005;
        $players6 = GameOptionValueNumberOfPlayers::Players006;
        $players7 = GameOptionValueNumberOfPlayers::Players007;
        $players8 = GameOptionValueNumberOfPlayers::Players008;
        $players9 = GameOptionValueNumberOfPlayers::Players009;

        $this->assertEquals($players2->value, $players2->getValue());
        $this->assertEquals($players3->value, $players3->getValue());
        $this->assertEquals($players4->value, $players4->getValue());
        $this->assertEquals($players5->value, $players5->getValue());
        $this->assertEquals($players6->value, $players6->getValue());
        $this->assertEquals($players7->value, $players7->getValue());
        $this->assertEquals($players8->value, $players8->getValue());
        $this->assertEquals($players9->value, $players9->getValue());
    }

    public function testGetLabel(): void
    {
        $players2 = GameOptionValueNumberOfPlayers::Players002;
        $players3 = GameOptionValueNumberOfPlayers::Players003;
        $players4 = GameOptionValueNumberOfPlayers::Players004;
        $players5 = GameOptionValueNumberOfPlayers::Players005;
        $players6 = GameOptionValueNumberOfPlayers::Players006;
        $players7 = GameOptionValueNumberOfPlayers::Players007;
        $players8 = GameOptionValueNumberOfPlayers::Players008;
        $players9 = GameOptionValueNumberOfPlayers::Players009;

        $this->assertEquals('2 Players', $players2->getLabel());
        $this->assertEquals('3 Players', $players3->getLabel());
        $this->assertEquals('4 Players', $players4->getLabel());
        $this->assertEquals('5 Players', $players5->getLabel());
        $this->assertEquals('6 Players', $players6->getLabel());
        $this->assertEquals('7 Players', $players7->getLabel());
        $this->assertEquals('8 Players', $players8->getLabel());
        $this->assertEquals('9 Players', $players9->getLabel());
    }
}
