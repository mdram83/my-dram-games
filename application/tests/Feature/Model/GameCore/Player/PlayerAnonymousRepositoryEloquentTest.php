<?php

namespace Tests\Feature\Model\GameCore\Player;

use App\Models\GameCore\Player\PlayerAnonymousFactory;
use App\Models\GameCore\Player\PlayerAnonymousRepository;
use App\Models\GameCore\Player\PlayerAnonymousRepositoryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class PlayerAnonymousRepositoryEloquentTest extends TestCase
{
    use RefreshDatabase;

    protected PlayerAnonymousRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = App::make(PlayerAnonymousRepository::class);
    }

    public function testInstanceOfPlayerAnonymousRepository(): void
    {
        $this->assertInstanceOf(PlayerAnonymousRepository::class, $this->repository);
    }

    public function testThrowExceptionWhenNoHashProvided(): void
    {
        $this->expectException(PlayerAnonymousRepositoryException::class);
        $this->repository->getOne('');
    }

    public function testReturnNullWhenMissingHashProvided(): void
    {
        $player = $this->repository->getOne('not-existing-hash');
        $this->assertNull($player);
    }

    public function testGetOneWithProperHash(): void
    {
        $player = App::make(PlayerAnonymousFactory::class)->create(['key' => 'test-key']);
        $hash = $player->hash;
        $loadedPlayer = $this->repository->getOne($hash);

        $this->assertEquals($player->getId(), $loadedPlayer->getId());

    }
}
