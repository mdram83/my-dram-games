<?php

namespace Tests\Feature\GameCore\GamePlayStorage\Eloquent;

use App\GameCore\GameInvite\GameInviteRepository;
use App\GameCore\GamePlayStorage\Eloquent\GamePlayStorageEloquent;
use App\GameCore\GamePlayStorage\Eloquent\GamePlayStorageRepositoryEloquent;
use App\GameCore\GamePlayStorage\GamePlayStorageException;
use App\GameCore\GamePlayStorage\GamePlayStorageRepository;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class GamePlayStorageRepositoryEloquentTest extends TestCase
{
    protected GamePlayStorageRepositoryEloquent $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = App::make(GamePlayStorageRepository::class);
    }

    public function testClassInstance(): void
    {
        $this->assertInstanceOf(GamePlayStorageRepository::class, $this->repository);
    }

    public function testThrowExceptionWhenGettingMissingStorage(): void
    {
        $this->expectException(GamePlayStorageException::class);
        $this->expectExceptionMessage(GamePlayStorageException::MESSAGE_NOT_FOUND);

        $this->repository->getOne('definitely-missing-123-tst-key');
    }

    public function testGetOne(): void
    {
        $inviteRepository = App::make(GameInviteRepository::class);
        $storage = new GamePlayStorageEloquent($inviteRepository);
        $id = $storage->getId();

        $this->assertEquals($id, $this->repository->getOne($id)->getId());
        $this->assertEquals($storage->getSetup(), $this->repository->getOne($id)->getSetup());
    }
}
