<?php

namespace Tests\Feature\Model;

use App\Extensions\Utils\Player\PlayerAnonymousFactory;
use App\GameCore\GamePlayDisconnection\GamePlayDisconnectException;
use App\GameCore\GamePlayDisconnection\GamePlayDisconnection;
use App\Models\GamePlayDisconnectionEloquentModel;
use App\Models\PlayerAnonymousEloquent;
use App\Models\User;
use DateTimeImmutable;
use Illuminate\Database\QueryException;
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

class GamePlayDisconnectionEloquentModelTest extends TestCase
{
    use RefreshDatabase;

    protected GamePlayDisconnectionEloquentModel $disconnect;

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
        $this->disconnect = new GamePlayDisconnectionEloquentModel();
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
        $this->assertInstanceOf(GamePlayDisconnection::class, $this->disconnect);
    }

    public function testThrowExceptionWhenSettingGamePlayTwice(): void
    {
        $this->expectException(GamePlayDisconnectException::class);
        $this->expectExceptionMessage(GamePlayDisconnectException::MESSAGE_GAMEPLAY_ALREADY_SET);

        $this->disconnect->setGamePlay($this->play);
        $this->disconnect->setGamePlay($this->play);
    }

    public function testThrowExceptionWhenSettingPlayerTwice(): void
    {
        $this->expectException(GamePlayDisconnectException::class);
        $this->expectExceptionMessage(GamePlayDisconnectException::MESSAGE_PLAYER_ALREADY_SET);

        $this->disconnect->setPlayer($this->user);
        $this->disconnect->setPlayer($this->user);
    }

    public function testThrowExceptionWhenSavingWithoutGamePlay(): void
    {
        $this->expectException(QueryException::class);

        $this->disconnect->setPlayer($this->user);
        $this->disconnect->setDisconnectedAt();
        $this->disconnect->save();
    }

    public function testThrowExceptionWhenSavingWithoutPlayer(): void
    {
        $this->expectException(QueryException::class);

        $this->disconnect->setGamePlay($this->play);
        $this->disconnect->setDisconnectedAt();
        $this->disconnect->save();
    }

    public function testThrowExceptionWhenSavingWithoutDisconnectedAt(): void
    {
        $this->expectException(QueryException::class);

        $this->disconnect->setGamePlay($this->play);
        $this->disconnect->setPlayer($this->user);
        $this->disconnect->save();
    }

    public function testThrowExceptionWhenCheckingExpiredIfNotSet(): void
    {
        $this->expectException(GamePlayDisconnectException::class);
        $this->expectExceptionMessage(GamePlayDisconnectException::MESSAGE_TIMESTAMP_NOT_SET);

        $this->disconnect->hasExpired(new DateTimeImmutable());
    }

    public function testThrowExceptionWhenSavingTwoObjectsWithSamePlayerAndGamePlay(): void
    {
        $this->expectException(QueryException::class);

        $this->disconnect->setGamePlay($this->play);
        $this->disconnect->setPlayer($this->user);
        $this->disconnect->setDisconnectedAt();
        $this->disconnect->save();

        $disc = new GamePlayDisconnectionEloquentModel();
        $disc->setGamePlay($this->play);
        $disc->setPlayer($this->user);
        $disc->setDisconnectedAt();
        $disc->save();
    }

    public function testGamePlaySet(): void
    {
        $this->disconnect->setGamePlay($this->play);
        $this->disconnect->setPlayer($this->user);
        $this->disconnect->setDisconnectedAt();
        $this->disconnect->save();
        $disc = GamePlayDisconnectionEloquentModel::where('game_play_id', '=', $this->play->getId())->first();

        $this->assertEquals($this->disconnect->id, $disc->id);
        $this->assertEquals($this->disconnect->gameplay->id, $disc->gameplay->id);
        $this->assertEquals($this->disconnect->playerable->id, $disc->playerable->id);
    }

    public function testPlayerSet(): void
    {
        $this->disconnect->setGamePlay($this->play);
        $this->disconnect->setPlayer($this->user);
        $this->disconnect->setDisconnectedAt();
        $this->disconnect->save();
        $disc = GamePlayDisconnectionEloquentModel::where('playerable_id', '=', $this->user->getId())->first();

        $this->assertEquals($this->disconnect->id, $disc->id);
        $this->assertEquals($this->disconnect->gameplay->id, $disc->gameplay->id);
        $this->assertEquals($this->disconnect->playerable->id, $disc->playerable->id);
    }

    public function testHasExpired(): void
    {
        $this->disconnect->setGamePlay($this->play);
        $this->disconnect->setPlayer($this->user);
        $this->disconnect->setDisconnectedAt();
        $this->disconnect->save();

        $loaded = GamePlayDisconnectionEloquentModel::where('id', '=', $this->disconnect->id)->first();

        sleep(1);

        $this->assertTrue($this->disconnect->hasExpired(0));
        $this->assertFalse($this->disconnect->hasExpired(60));
        $this->assertTrue($loaded->hasExpired(0));
        $this->assertFalse($loaded->hasExpired(60));
    }

    public function testThrowExceptionWhenRemovingBeforeSave(): void
    {
        $this->expectException(GamePlayDisconnectException::class);
        $this->expectExceptionMessage(GamePlayDisconnectException::MESSAGE_DELETING_BEFORE_SAVE);

        $this->disconnect->setGamePlay($this->play);
        $this->disconnect->setPlayer($this->user);
        $this->disconnect->setDisconnectedAt();
        $this->disconnect->remove();
    }

    public function testRemovedDisconnectionNotAvailableAnymore(): void
    {
        $this->disconnect->setGamePlay($this->play);
        $this->disconnect->setPlayer($this->user);
        $this->disconnect->setDisconnectedAt();
        $this->disconnect->save();
        $this->disconnect->remove();

        $this->assertNull(GamePlayDisconnectionEloquentModel::where('id', '=', $this->disconnect->id)->first());
    }
}
