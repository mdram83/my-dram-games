<?php

namespace Tests\Feature\GameCore\Player\Eloquent;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class PlayerAnonymousRepositoryEloquentTest extends TestCase
{
    use RefreshDatabase;

    protected \App\GameCore\Player\PlayerAnonymousRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = App::make(\App\GameCore\Player\PlayerAnonymousRepository::class);
    }

    public function testInstanceOfPlayerAnonymousRepository(): void
    {
        $this->assertInstanceOf(\App\GameCore\Player\PlayerAnonymousRepository::class, $this->repository);
    }

    public function testThrowExceptionWhenNoHashProvided(): void
    {
        $this->expectException(\App\GameCore\Player\PlayerAnonymousRepositoryException::class);
        $this->repository->getOne('');
    }

    public function testReturnNullWhenMissingHashProvided(): void
    {
        $player = $this->repository->getOne('not-existing-hash');
        $this->assertNull($player);
    }

    public function testGetOneWithProperHash(): void
    {
        $player = App::make(\App\GameCore\Player\PlayerAnonymousFactory::class)->create(['key' => 'test-key']);
        $hash = $player->hash;
        $loadedPlayer = $this->repository->getOne($hash);

        $this->assertEquals($player->getId(), $loadedPlayer->getId());

    }
}
