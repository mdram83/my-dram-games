<?php

namespace Tests\Feature\Services\GamePlayDisconnection\Eloquent;

use App\Extensions\Utils\Player\PlayerAnonymousFactory;
use App\Services\GamePlayDisconnection\Eloquent\GamePlayDisconnectionFactoryEloquent;
use App\Services\GamePlayDisconnection\GamePlayDisconnectException;
use App\Services\GamePlayDisconnection\GamePlayDisconnectionFactory;
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

class GamePlayDisconnectionFactoryEloquentTest extends TestCase
{
    use RefreshDatabase;

    protected GamePlayDisconnectionFactoryEloquent $factory;

    protected User $user;
    protected PlayerAnonymousEloquent $playerAnonymous;
    protected GameInvite $invite;
    protected GamePlay $play;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->playerAnonymous = App::make(PlayerAnonymousFactory::class)->create(['key' => 'test-player-key']);
        $this->invite = $this->prepareGameInvite();
        $this->play = $this->prepareGamePlay($this->invite);
        $this->factory = App::make(GamePlayDisconnectionFactory::class);
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
        $this->assertInstanceOf(GamePlayDisconnectionFactory::class, $this->factory);
    }

    public function testThrowExceptionForDuplicatedGamePlayAndPlayer(): void
    {
        $this->expectException(GamePlayDisconnectException::class);
        $this->expectExceptionMessage(GamePlayDisconnectException::MESSAGE_RECORD_ALREADY_EXIST);

        $this->factory->create($this->play, $this->user);
        $this->factory->create($this->play, $this->user);
    }

    public function testCreate(): void
    {
        $disconnection = $this->factory->create($this->play, $this->user);
        $this->assertInstanceOf(GamePlayDisconnectionEloquentModel::class, $disconnection);
    }
}
