<?php

namespace Tests\Feature\GameCore\GamePlayDisconnection\Eloquent;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueForfeitAfter;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\GamePlay\GamePlay;
use App\GameCore\GamePlayDisconnection\Eloquent\GamePlayDisconnectionFactoryEloquent;
use App\GameCore\GamePlayDisconnection\Eloquent\GamePlayDisconnectionRepositoryEloquent;
use App\GameCore\GamePlayDisconnection\GamePlayDisconnectionFactory;
use App\GameCore\GamePlayDisconnection\GamePlayDisconnectionRepository;
use App\GameCore\Player\PlayerAnonymousFactory;
use App\GameCore\Services\Collection\Collection;
use App\Games\TicTacToe\GamePlayAbsFactoryTicTacToe;
use App\Models\GamePlayDisconnectionEloquentModel;
use App\Models\PlayerAnonymousEloquent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
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
        $options = new CollectionGameOptionValueInput(
            App::make(Collection::class),
            [
                'numberOfPlayers' => GameOptionValueNumberOfPlayers::Players002,
                'autostart' => GameOptionValueAutostart::Disabled,
                'forfeitAfter' => GameOptionValueForfeitAfter::Disabled,
            ]
        );

        $invite = App::make(GameInviteFactory::class)->create('tic-tac-toe', $options, $this->user);
        $invite->addPlayer($this->playerAnonymous);
        return $invite;
    }

    protected function prepareGamePlay(GameInvite $invite): GamePlay
    {
        return App::make(GamePlayAbsFactoryTicTacToe::class)->create($invite);
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
