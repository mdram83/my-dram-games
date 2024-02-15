<?php

namespace Tests\Feature\Model\GameCore\Player;

use App\Models\GameCore\Player\Player;
use App\Models\GameCore\Player\PlayerAnonymous;
use App\Models\GameCore\Player\PlayerAnonymousEloquent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerAnonymousEloquentTest extends TestCase
{
    use RefreshDatabase;

    protected string $testName = 'TestName123';
    protected string $testId;

    public function setUp(): void
    {
        parent::setUp();
        $this->testId = md5(time() . rand(1, 1000));
    }

    public function testSaveFailWithoutId(): void
    {
        $this->expectException(\Exception::class);
        $anonymous = new PlayerAnonymousEloquent();
        $anonymous->name = $this->testName;
        $anonymous->save();
    }

    public function testSaveFailWithoutName(): void
    {
        $this->expectException(\Exception::class);
        $anonymous = new PlayerAnonymousEloquent();
        $anonymous->id = $this->testId;
        $anonymous->save();
    }

    public function testIsInstanceOfPlayer(): void
    {
        $anonymous = new PlayerAnonymousEloquent();
        $this->assertInstanceOf(Player::class, $anonymous);
        $this->assertInstanceOf(PlayerAnonymous::class, $anonymous);
    }

    public function testGetId(): void
    {
        PlayerAnonymousEloquent::factory()->create(['id' => $this->testId]);
        $anonymous = PlayerAnonymousEloquent::find($this->testId);
        $this->assertEquals($this->testId, $anonymous->getId());
    }

    public function testGetName(): void
    {
        PlayerAnonymousEloquent::factory()->create(['id' => $this->testId, 'name' => $this->testName]);
        $anonymous = PlayerAnonymousEloquent::find($this->testId);
        $this->assertEquals($this->testName, $anonymous->getName());
    }
}
