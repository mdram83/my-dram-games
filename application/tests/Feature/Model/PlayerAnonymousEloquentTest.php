<?php

namespace Tests\Feature\Model;

use App\GameCore\Player\Player;
use App\GameCore\Player\PlayerAnonymous;
use App\Models\PlayerAnonymousEloquent;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerAnonymousEloquentTest extends TestCase
{
    use RefreshDatabase;

    protected string $testName = 'TestName123';
    protected string $testHash;

    public function setUp(): void
    {
        parent::setUp();
        $this->testHash = md5(time() . rand(1, 1000));
    }

    public function testSaveFailWithoutId(): void
    {
        $this->expectException(Exception::class);
        $anonymous = new PlayerAnonymousEloquent();
        $anonymous->name = $this->testName;
        $anonymous->save();
    }

    public function testSaveFailWithoutName(): void
    {
        $this->expectException(Exception::class);
        $anonymous = new PlayerAnonymousEloquent();
        $anonymous->id = $this->testHash;
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
        PlayerAnonymousEloquent::factory()->create(['hash' => $this->testHash]);
        $anonymous = PlayerAnonymousEloquent::where(['hash' => $this->testHash])->first();

        $this->assertMatchesRegularExpression(
            '/^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$/',
            $anonymous->getId()
        );
    }

    public function testGetName(): void
    {
        PlayerAnonymousEloquent::factory()->create(['hash' => $this->testHash, 'name' => $this->testName]);
        $anonymous = PlayerAnonymousEloquent::where(['hash' => $this->testHash])->first();
        $this->assertEquals($this->testName, $anonymous->getName());
    }

    public function testIsPremiumAlwaysFalse(): void
    {
        $anonymous = PlayerAnonymousEloquent::factory()->create(['hash' => $this->testHash, 'name' => $this->testName]);
        $this->assertFalse($anonymous->isPremium());
    }
}
