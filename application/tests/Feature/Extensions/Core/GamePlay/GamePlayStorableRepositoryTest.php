<?php

namespace Tests\Feature\Extensions\Core\GamePlay;

use App\Extensions\Core\GamePlay\GamePlayStorableRepository;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use MyDramGames\Core\Exceptions\GamePlayStorageException;
use MyDramGames\Core\GameInvite\GameInvite;
use MyDramGames\Core\GameInvite\GameInviteFactory;
use MyDramGames\Core\GameOption\GameOptionConfigurationCollectionPowered;
use MyDramGames\Core\GameOption\GameOptionConfigurationGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueAutostartGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueForfeitAfterGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueNumberOfPlayersGeneric;
use MyDramGames\Core\GamePlay\GamePlay;
use MyDramGames\Core\GamePlay\GamePlayRepository;
use MyDramGames\Core\GamePlay\Services\GamePlayServicesProvider;
use MyDramGames\Core\GamePlay\Storage\GamePlayStorageFactory;
use MyDramGames\Games\TicTacToe\Extensions\Core\GamePlayTicTacToe;
use MyDramGames\Utils\Php\Collection\CollectionEngine;
use Tests\TestCase;

class GamePlayStorableRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected GamePlayStorableRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = App::make(GamePlayRepository::class);
    }

    public function prepareGameInvite(): GameInvite
    {
        $options = new GameOptionConfigurationCollectionPowered(
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
        );

        $invite = App::make(GameInviteFactory::class)->create(
            'tic-tac-toe',
            $options,
            User::factory()->create()
        );
        $invite->addPlayer(User::factory()->create());

        return $invite;
    }

    public function prepareGamePlay(): GamePlay
    {
        $invite = $this->prepareGameInvite();
        $storage = App::make(GamePlayStorageFactory::class)->create($invite);
        return new GamePlayTicTacToe($storage, App::make(GamePlayServicesProvider::class));
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(GamePlayRepository::class, $this->repository);
    }

    public function testThrowExceptionWhenGettingMissingGamePlay(): void
    {
        $this->expectException(GamePlayStorageException::class);
        $this->expectExceptionMessage(GamePlayStorageException::MESSAGE_NOT_FOUND);

        $this->repository->getOne('definitely-missing-gamePlay123-id');
    }

    public function testGetOne(): void
    {
        $id = $this->prepareGamePlay()->getId();
        $loaded = $this->repository->getOne($id);

        $this->assertInstanceOf(GamePlay::class, $loaded);
        $this->assertEquals($id, $loaded->getId());
    }

    public function testGetOneByGameInvite(): void
    {
        $game = $this->prepareGamePlay();
        $assignedInvite = $game->getGameInvite();
        $orphanInvite = $this->prepareGameInvite();

        $this->assertNull($this->repository->getOneByGameInvite($orphanInvite));
        $this->assertEquals($game->getId(), $this->repository->getOneByGameInvite($assignedInvite)->getId());
    }
}
