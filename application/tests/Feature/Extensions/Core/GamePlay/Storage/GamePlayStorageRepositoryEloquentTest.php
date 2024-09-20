<?php

namespace Tests\Feature\Extensions\Core\GamePlay\Storage;

use App\Extensions\Core\GamePlay\Storage\GamePlayStorageEloquent;
use App\Extensions\Core\GamePlay\Storage\GamePlayStorageRepositoryEloquent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use MyDramGames\Core\Exceptions\GamePlayStorageException;
use MyDramGames\Core\GameInvite\GameInviteFactory;
use MyDramGames\Core\GameInvite\GameInviteRepository;
use MyDramGames\Core\GameOption\GameOptionConfigurationCollectionPowered;
use MyDramGames\Core\GameOption\GameOptionConfigurationGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueAutostartGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueForfeitAfterGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueNumberOfPlayersGeneric;
use MyDramGames\Core\GamePlay\Storage\GamePlayStorageRepository;
use MyDramGames\Utils\Php\Collection\CollectionEngine;
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
        $inviteFactory = App::make(GameInviteFactory::class);
        $invite = $inviteFactory->create(
            'tic-tac-toe',
            new GameOptionConfigurationCollectionPowered(
                App::make(CollectionEngine::class),
                [
                    new GameOptionConfigurationGeneric(
                        'numberOfPlayers',
                        GameOptionValueNumberOfPlayersGeneric::Players002
                    ),
                    new GameOptionConfigurationGeneric(
                        'autostart',
                        GameOptionValueAutostartGeneric::Disabled
                    ),
                    new GameOptionConfigurationGeneric(
                        'forfeitAfter',
                        GameOptionValueForfeitAfterGeneric::Disabled
                    ),
                ]
            ),
            User::factory()->create()
        );
        $invite->addPlayer(User::factory()->create());
        $storage = new GamePlayStorageEloquent(App::make(GameInviteRepository::class));

        $this->assertNull($this->repository->getOneByGameInvite($invite));
        $storage->setGameInvite($invite);
        $this->assertEquals($storage->getId(), $this->repository->getOneByGameInvite($invite)->getId());

    }
}
