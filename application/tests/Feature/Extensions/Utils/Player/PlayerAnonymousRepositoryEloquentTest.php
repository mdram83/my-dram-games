<?php

namespace Tests\Feature\Extensions\Utils\Player;

use App\Extensions\Utils\Player\PlayerAnonymousFactory;
use App\Extensions\Utils\Player\PlayerAnonymousRepository;
use App\Extensions\Utils\Player\PlayerAnonymousRepositoryException;
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
        $loadedPlayer = $this->repository->getOne($player->hash);

        $this->assertEquals($player->getId(), $loadedPlayer->getId());
    }
}
