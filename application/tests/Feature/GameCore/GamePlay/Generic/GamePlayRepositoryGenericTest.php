<?php

namespace Tests\Feature\GameCore\GamePlay\Generic;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueForfeitAfter;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\GamePlay\GamePlay;
use App\GameCore\GamePlay\GamePlayAbsFactoryRepository;
use App\GameCore\GamePlay\GamePlayRepository;
use App\GameCore\GamePlay\Generic\GamePlayRepositoryGeneric;
use App\GameCore\GamePlayStorage\GamePlayStorageException;
use App\GameCore\Services\Collection\Collection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class GamePlayRepositoryGenericTest extends TestCase
{
    use RefreshDatabase;

    protected GamePlayRepositoryGeneric $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = App::make(GamePlayRepository::class);
    }

    public function prepareGameInvite(): GameInvite
    {
        $options = new CollectionGameOptionValueInput(
            App::make(Collection::class),
            [
                'numberOfPlayers' => GameOptionValueNumberOfPlayers::Players002,
                'autostart' => GameOptionValueAutostart::Disabled,
                'forfeitAfter' => GameOptionValueForfeitAfter::Disabled,
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
        return App::make(GamePlayAbsFactoryRepository::class)->getOne('tic-tac-toe')->create($this->prepareGameInvite());
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
