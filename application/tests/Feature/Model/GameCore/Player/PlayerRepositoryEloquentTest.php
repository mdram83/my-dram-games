<?php

namespace Tests\Feature\Model\GameCore\Player;

use App\Models\GameCore\Player\Player;
use App\Models\GameCore\Player\PlayerAnonymous;
use App\Models\GameCore\Player\PlayerAnonymousHashGenerator;
use App\Models\GameCore\Player\PlayerRegistered;
use App\Models\GameCore\Player\PlayerRepository;
use App\Models\GameCore\Player\PlayerRepositoryEloquent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class PlayerRepositoryEloquentTest extends TestCase
{
    use RefreshDatabase;

    protected bool $commonSetup = false;
    protected PlayerRepositoryEloquent $repository;
    protected User $user;
    protected PlayerAnonymousHashGenerator $generator;

    public function setUp(): void
    {
        parent::setUp();
        if (!$this->commonSetup) {
            $this->repository = App::make(PlayerRepository::class);
            $this->generator = App::make(PlayerAnonymousHashGenerator::class);
            $this->user = User::factory()->create();
            $this->commonSetup = true;
        }
    }

    public function testRepositoryInstanceProvided(): void
    {
        $this->assertInstanceOf(PlayerRepository::class, $this->repository);
        $this->assertInstanceOf(PlayerRepositoryEloquent::class, $this->repository);
    }

    public function testAuthenticatedUserReturnsInstanceOfPlayer(): void
    {
        $this->actingAs($this->user)->get('/');
        $player = $this->repository->getOneCurrent();

        $this->assertInstanceOf(Player::class, $player);
        $this->assertInstanceOf(PlayerRegistered::class, $player);
        $this->assertEquals($this->user->getId(), $player->getId());
    }

    public function testUnauthenticatedUserReturnsSameInstanceOfPlayer(): void
    {
        session()->getId();
        $player = $this->repository->getOneCurrent();
        $samePlayer = $this->repository->getOneCurrent();

        session()->setId('test-id-for-new-session-user');
        $newPlayer = $this->repository->getOneCurrent();

        $this->assertInstanceOf(Player::class, $player);
        $this->assertInstanceOf(PlayerAnonymous::class, $player);
        $this->assertInstanceOf(Player::class, $samePlayer);
        $this->assertInstanceOf(PlayerAnonymous::class, $samePlayer);
        $this->assertInstanceOf(Player::class, $newPlayer);
        $this->assertInstanceOf(PlayerAnonymous::class, $newPlayer);
        $this->assertEquals($player->getId(), $samePlayer->getId());
        $this->assertNotEquals($player->getId(), $newPlayer->getId());
    }
}
