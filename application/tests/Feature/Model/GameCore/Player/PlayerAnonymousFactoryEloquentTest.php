<?php

namespace Tests\Feature\Model\GameCore\Player;

use App\Models\GameCore\Player\PlayerAnonymous;
use App\Models\GameCore\Player\PlayerAnonymousFactory;
use App\Models\GameCore\Player\PlayerAnonymousFactoryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class PlayerAnonymousFactoryEloquentTest extends TestCase
{
    use RefreshDatabase;

    protected PlayerAnonymousFactory $factory;
    protected bool $commonSetup = false;
    protected string $userKey = 'test-key';

    public function setUp(): void
    {
        parent::setUp();
        if (!$this->commonSetup) {
            $this->factory = App::make(PlayerAnonymousFactory::class);
            $this->commonSetup = true;
        }
    }

    public function testIsInstanceOfPlayerAnonymousFactory(): void
    {
        $this->assertInstanceOf(PlayerAnonymousFactory::class, $this->factory);
    }

    public function testCreateThrowsExceptionIfNoHashAttributeProvided(): void
    {
        $this->expectException(PlayerAnonymousFactoryException::class);
        $this->factory->create();
    }

    public function testCreateThrowsExceptionIfHashIsEmptyString(): void
    {
        $this->expectException(PlayerAnonymousFactoryException::class);
        $this->factory->create(['key' => '']);
    }

    public function testCreatePlayerAnonymousEloquent(): void
    {
        $player = $this->factory->create(['key' => $this->userKey]);
        $this->assertInstanceOf(PlayerAnonymous::class, $player);
    }

    public function testCreateThrowsExceptionIfSameHashUsedSecondTime(): void
    {
        $this->expectException(\Exception::class);
        $this->factory->create(['key' => $this->userKey]);
        $this->factory->create(['key' => $this->userKey]);
    }
}
