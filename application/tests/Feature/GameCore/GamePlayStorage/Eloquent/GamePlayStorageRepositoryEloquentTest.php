<?php

namespace Tests\Feature\GameCore\GamePlayStorage\Eloquent;

use App\GameCore\GameInvite\Eloquent\GameInviteFactoryEloquent;
use App\GameCore\GameInvite\Eloquent\GameInviteRepositoryEloquent;
use App\GameCore\GameInvite\GameInviteRepository;
use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueForfeitAfter;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\GamePlayStorage\Eloquent\GamePlayStorageEloquent;
use App\GameCore\GamePlayStorage\Eloquent\GamePlayStorageRepositoryEloquent;
use App\GameCore\GamePlayStorage\GamePlayStorageException;
use App\GameCore\GamePlayStorage\GamePlayStorageRepository;
use App\GameCore\Services\Collection\Collection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class GamePlayStorageRepositoryEloquentTest extends TestCase
{
    use RefreshDatabase;

    protected GamePlayStorageRepositoryEloquent $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = App::make(GamePlayStorageRepository::class);
    }

    public function testClassInstance(): void
    {
        $this->assertInstanceOf(GamePlayStorageRepositoryEloquent::class, $this->repository);
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

    public function testGetOneByGameInvite(): void
    {
        $inviteFactory = App::make(GameInviteFactoryEloquent::class);
        $invite = $inviteFactory->create(
            'tic-tac-toe',
            new CollectionGameOptionValueInput(
                App::make(Collection::class),
                [
                    'numberOfPlayers' => GameOptionValueNumberOfPlayers::Players002,
                    'autostart' => GameOptionValueAutostart::Disabled,
                    'forfeitAfter' => GameOptionValueForfeitAfter::Disabled,
                ]
            ),
            User::factory()->create()
        );
        $invite->addPlayer(User::factory()->create());
        $storage = new GamePlayStorageEloquent(App::make(GameInviteRepositoryEloquent::class));

        $this->assertNull($this->repository->getOneByGameInvite($invite));
        $storage->setGameInvite($invite);
        $this->assertEquals($storage->getId(), $this->repository->getOneByGameInvite($invite)->getId());

    }
}
