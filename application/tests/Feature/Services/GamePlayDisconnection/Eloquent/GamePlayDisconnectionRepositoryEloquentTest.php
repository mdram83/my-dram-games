<?php

namespace Tests\Feature\Services\GamePlayDisconnection\Eloquent;

use App\Extensions\Utils\Player\PlayerAnonymousFactory;
use App\Services\GamePlayDisconnection\Eloquent\GamePlayDisconnectionRepositoryEloquent;
use App\Services\GamePlayDisconnection\Eloquent\GamePlayDisconnectionFactoryEloquent;
use App\Services\GamePlayDisconnection\GamePlayDisconnectionFactory;
use App\Services\GamePlayDisconnection\GamePlayDisconnectionRepository;
use App\Models\GamePlayDisconnectionEloquentModel;
use App\Models\PlayerAnonymousEloquent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use MyDramGames\Core\GameInvite\GameInvite;
use MyDramGames\Core\GameInvite\GameInviteFactory;
use MyDramGames\Core\GameOption\GameOptionConfigurationCollectionPowered;
use MyDramGames\Core\GameOption\GameOptionConfigurationGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueAutostartGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueForfeitAfterGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueNumberOfPlayersGeneric;
use MyDramGames\Core\GamePlay\GamePlay;
use MyDramGames\Core\GamePlay\GamePlayFactory;
use MyDramGames\Utils\Php\Collection\CollectionEngine;
use Tests\TestCase;

class GamePlayDisconnectionRepositoryEloquentTest extends TestCase
{
    use RefreshDatabase;

    protected GamePlayDisconnectionRepositoryEloquent $repository;

    protected User $user;
    protected PlayerAnonymousEloquent $playerAnonymous;
    protected GameInvite $invite;
    protected GamePlay $play;
    protected GamePlayDisconnectionFactoryEloquent $factory;
    protected GamePlayDisconnectionEloquentModel $disconnection;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->playerAnonymous = App::make(PlayerAnonymousFactory::class)->create(['key' => 'test-player-key']);
        $this->invite = $this->prepareGameInvite();
        $this->play = $this->prepareGamePlay($this->invite);
        $this->factory = App::make(GamePlayDisconnectionFactory::class);
        $this->disconnection = $this->factory->create($this->play, $this->user);
        $this->repository = App::make(GamePlayDisconnectionRepository::class);
    }

    protected function prepareGameInvite(): GameInvite
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

        $invite = App::make(GameInviteFactory::class)->create('tic-tac-toe', $options, $this->user);
        $invite->addPlayer($this->playerAnonymous);
        return $invite;
    }

    protected function prepareGamePlay(GameInvite $invite): GamePlay
    {
        return App::make(GamePlayFactory::class)->create($invite);
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(GamePlayDisconnectionRepository::class, $this->repository);
    }

    public function testReturnNullForDifferentPlayer(): void
    {
        $this->assertNull($this->repository->getOneByGamePlayAndPlayer($this->play, $this->playerAnonymous));
    }

    public function testReturnNullForDifferentGamePlay(): void
    {
        $this->assertNull($this->repository->getOneByGamePlayAndPlayer(
            $this->prepareGamePlay($this->prepareGameInvite()),
            $this->user,
        ));
    }

    public function testGetOne(): void
    {
        $disconnectionFromRepo = $this->repository->getOneByGamePlayAndPlayer($this->play, $this->user);
        $this->assertEquals($this->disconnection->id, $disconnectionFromRepo->id);
    }
}
