<?php

namespace Http\Controllers\GameCore;

use App\Events\GameCore\GamePlay\GamePlayDisconnectedEvent;
use App\Events\GameCore\GamePlay\GamePlayMovedEvent;
use App\GameCore\GamePlayDisconnection\Eloquent\GamePlayDisconnectionRepositoryEloquent;
use App\GameCore\GamePlayDisconnection\GamePlayDisconnectionRepository;
use App\Models\User;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use MyDramGames\Core\GameInvite\GameInvite;
use MyDramGames\Core\GameInvite\GameInviteFactory;
use MyDramGames\Core\GameOption\GameOptionConfigurationCollectionPowered;
use MyDramGames\Core\GameOption\GameOptionConfigurationGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueAutostartGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueForfeitAfterGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueNumberOfPlayersGeneric;
use MyDramGames\Core\GamePlay\GamePlay;
use MyDramGames\Core\GamePlay\GamePlayFactory;
use MyDramGames\Core\GamePlay\GamePlayRepository;
use MyDramGames\Utils\Php\Collection\CollectionEngine;
use MyDramGames\Utils\Player\Player;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GamePlayDisconnectionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Player $host;
    protected Player $player;
    protected Player $notPlayer;
    protected GameInvite $invite;
    protected GamePlayDisconnectionRepository $disconnectionRepository;

    protected string $storeRouteName = 'ajax.gameplay.store';
    protected string $showRouteName = 'gameplay.show';
    protected string $moveRouteName = 'ajax.gameplay.move';
    protected string $disconnectRouteName = 'ajax.gameplay.disconnect';
    protected string $connectRouteName = 'ajax.gameplay.connect';
    protected string $requestForfeitRouteName = 'ajax.gameplay.disconnect-forfeit';

    public function setUp(): void
    {
        parent::setUp();

        $this->host = User::factory()->create();
        $this->player = User::factory()->create();
        $this->notPlayer = User::factory()->create();

        $this->invite = $this->prepareGameInvite();
        $this->disconnectionRepository = App::make(GamePlayDisconnectionRepository::class);
    }

    public function prepareGameInvite(bool $complete = true, $forfeitAfter = false): GameInvite
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
                    GameOptionValueForfeitAfterGeneric::Minute
                ),
            ]
        );

        $invite = App::make(GameInviteFactory::class)->create('tic-tac-toe', $options, $this->host);

        if ($complete) {
            $invite->addPlayer($this->player);
        }

        return $invite;
    }

    protected function createGamePlay(GameInvite $invite): GamePlay
    {
        return App::make(GamePlayFactory::class)->create($invite);
    }

    protected function getDisconnectResponse(Player $player, int|string $gamePlayId, Player $disconnected, bool|array $payload = true): TestResponse
    {
        return $this
            ->actingAs($player)
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->json(
                'POST',
                route($this->disconnectRouteName, ['gamePlayId' => $gamePlayId]),
                $payload === true ? ['disconnected' => $disconnected->getName()] : $payload
            );
    }

    protected function getConnectResponse(Player $player, int|string $gamePlayId): TestResponse
    {
        return $this
            ->actingAs($player)
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->json('GET', route($this->connectRouteName, ['gamePlayId' => $gamePlayId]));
    }

    protected function getDisconnectForfeitResponse(Player $player, int|string $gamePlayId, Player $requestedPlayer, bool|array $payload = true): TestResponse
    {
        return $this
            ->actingAs($player)
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->json(
                'POST',
                route($this->requestForfeitRouteName, ['gamePlayId' => $gamePlayId]),
                $payload === true ? ['disconnected' => $requestedPlayer->getName()] : $payload
            );
    }

    public function testDisconnectWrongGameIdError(): void
    {
        Event::fake();
        $response = $this->getDisconnectResponse($this->host, 'some-wrong-id-123T', $this->player);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        Event::assertNotDispatched(GamePlayDisconnectedEvent::class);
    }

    public function testDisconnectByNotPlayerError(): void
    {
        Event::fake();
        $play = $this->createGamePlay($this->invite);
        $response = $this->getDisconnectResponse($this->notPlayer, $play->getId(), $this->player);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        Event::assertNotDispatched(GamePlayDisconnectedEvent::class);
    }

    public function testDisconnectForNotParticipantError(): void
    {
        Event::fake();
        $play = $this->createGamePlay($this->invite);
        $response = $this->getDisconnectResponse($this->host, $play->getId(), $this->notPlayer);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        Event::assertNotDispatched(GamePlayDisconnectedEvent::class);
    }

    public function testDisconnectMissingPayloadError(): void
    {
        Event::fake();
        $play = $this->createGamePlay($this->invite);
        $response = $this->getDisconnectResponse($this->host, $play->getId(), $this->notPlayer, []);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        Event::assertNotDispatched(GamePlayDisconnectedEvent::class);
    }

    public function testDisconnectIncorrectPayloadError(): void
    {
        Event::fake();
        $play = $this->createGamePlay($this->invite);
        $response = $this->getDisconnectResponse($this->host, $play->getId(), $this->notPlayer, ['sth' => 'wrong']);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        Event::assertNotDispatched(GamePlayDisconnectedEvent::class);
    }

    public function testDisconnectFinishedGameError(): void
    {
        Event::fake();
        $play = $this->createGamePlay($this->invite);
        $play->handleForfeit($this->player);
        $response = $this->getDisconnectResponse($this->host, $play->getId(), $this->player);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        Event::assertNotDispatched(GamePlayDisconnectedEvent::class);
    }

    public function testDisconnectFirstTime(): void
    {
        Event::fake();
        $play = $this->createGamePlay($this->invite);
        $response = $this->getDisconnectResponse($this->host, $play->getId(), $this->player);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotNull($this->disconnectionRepository->getOneByGamePlayAndPlayer($play, $this->player));
        Event::assertDispatched(GamePlayDisconnectedEvent::class);
    }

    public function testDisconnectSecondTime(): void
    {
        Event::fake();
        $play = $this->createGamePlay($this->invite);
        $response = $this->getDisconnectResponse($this->host, $play->getId(), $this->player);
        $disconnection = $this->disconnectionRepository->getOneByGamePlayAndPlayer($play, $this->player);

        sleep(1);

        $nextResponse = $this->getDisconnectResponse($this->host, $play->getId(), $this->player);
        $nextDisconnection = $this->disconnectionRepository->getOneByGamePlayAndPlayer($play, $this->player);

        $response->assertStatus(Response::HTTP_OK);
        $nextResponse->assertStatus(Response::HTTP_OK);
        $this->assertNotNull($disconnection);
        $this->assertNotNull($nextDisconnection);
        $this->assertEquals($disconnection->id, $nextDisconnection->id);
        $this->assertNotEquals($disconnection->disconnected_at, $nextDisconnection->disconnected_at);
        Event::assertDispatchedTimes(GamePlayDisconnectedEvent::class, 2);
    }

    public function testConnectWrongGameIdError(): void
    {
        $response = $this->getConnectResponse($this->host, 'some-wrong-id-123T');
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testConnectNotPlayerError(): void
    {
        $play = $this->createGamePlay($this->invite);
        $response = $this->getConnectResponse($this->notPlayer, $play->getId());

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testConnectFinishedGameError(): void
    {
        $play = $this->createGamePlay($this->invite);
        $play->handleForfeit($this->player);
        $response = $this->getConnectResponse($this->host, $play->getId());

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testConnectAfterDisconnection(): void
    {
        $play = $this->createGamePlay($this->invite);
        $this->getDisconnectResponse($this->host, $play->getId(), $this->player);
        $disconnection = $this->disconnectionRepository->getOneByGamePlayAndPlayer($play, $this->player);
        $response = $this->getConnectResponse($this->player, $play->getId());
        $refreshedDisconnection = $this->disconnectionRepository->getOneByGamePlayAndPlayer($play, $this->player);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotNull($disconnection);
        $this->assertNull($refreshedDisconnection);
    }

    public function testConnectWithoutDisconnection(): void
    {
        $play = $this->createGamePlay($this->invite);
        $response = $this->getConnectResponse($this->player, $play->getId());
        $disconnection = $this->disconnectionRepository->getOneByGamePlayAndPlayer($play, $this->player);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertNull($disconnection);
    }

    public function testRequestForfeitWrongGameError(): void
    {
        Event::fake();
        $response = $this->getDisconnectForfeitResponse($this->host, 'some-wrong-id-123T', $this->player);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        Event::assertNotDispatched(GamePlayMovedEvent::class);
    }

    public function testRequestForfeitForNotPlayerError(): void
    {
        Event::fake();
        $play = $this->createGamePlay($this->invite);
        $response = $this->getDisconnectForfeitResponse($this->host, $play->getId(), $this->notPlayer);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        Event::assertNotDispatched(GamePlayMovedEvent::class);
    }

    public function testRequestForfeitByNotPlayerError(): void
    {
        Event::fake();
        $play = $this->createGamePlay($this->invite);
        $response = $this->getDisconnectForfeitResponse($this->notPlayer, $play->getId(), $this->player);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        Event::assertNotDispatched(GamePlayMovedEvent::class);
    }

    public function testRequestForfeitMissingPayloadError(): void
    {
        Event::fake();
        $play = $this->createGamePlay($this->invite);
        $response = $this->getDisconnectForfeitResponse($this->host, $play->getId(), $this->player, []);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        Event::assertNotDispatched(GamePlayMovedEvent::class);
    }

    public function testRequestForfeitWrongPayloadError(): void
    {
        Event::fake();
        $play = $this->createGamePlay($this->invite);
        $response = $this->getDisconnectForfeitResponse($this->host, $play->getId(), $this->player, ['sth' => 'wrong']);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        Event::assertNotDispatched(GamePlayMovedEvent::class);
    }

    public function testRequestForfeitFinishedGameplayError(): void
    {
        Event::fake();
        $play = $this->createGamePlay($this->invite);
        $play->handleForfeit($this->player);
        $response = $this->getDisconnectForfeitResponse($this->host, $play->getId(), $this->player);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        Event::assertNotDispatched(GamePlayMovedEvent::class);
    }

    public function testRequestForfeitOptionDisabledError(): void
    {
        Event::fake();
        $play = $this->createGamePlay($this->invite);
        $response = $this->getDisconnectForfeitResponse($this->host, $play->getId(), $this->player);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        Event::assertNotDispatched(GamePlayMovedEvent::class);
    }

    public function testRequestForfeitTooSoonError(): void
    {
        Event::fake();
        $play = $this->createGamePlay($this->prepareGameInvite(true, true));
        $this->getDisconnectResponse($this->host, $play->getId(), $this->player);
        $response = $this->getDisconnectForfeitResponse($this->host, $play->getId(), $this->player);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        Event::assertNotDispatched(GamePlayMovedEvent::class);
    }

    public function testRequestForfeitWithoutDisconnectionError(): void
    {
        Event::fake();
        $play = $this->createGamePlay($this->prepareGameInvite(true, true));
        $response = $this->getDisconnectForfeitResponse($this->host, $play->getId(), $this->player);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        Event::assertNotDispatched(GamePlayMovedEvent::class);
    }

    public function testRequestForfeitOk(): void
    {
        Event::fake();
        $play = $this->createGamePlay($this->prepareGameInvite(true, true));
        $this->getDisconnectResponse($this->host, $play->getId(), $this->player);

        $disconnection = App::make(GamePlayDisconnectionRepositoryEloquent::class)
            ->getOneByGamePlayAndPlayer($play, $this->player);
        $disconnection->disconnected_at = (new DateTimeImmutable())->modify('-2 minutes');
        $disconnection->save();

        $response = $this->getDisconnectForfeitResponse($this->host, $play->getId(), $this->player);

        $loadedPlay = App::make(GamePlayRepository::class)->getOne($play->getId());

        $this->assertTrue($loadedPlay->isFinished());
        $response->assertStatus(Response::HTTP_OK);
        Event::assertDispatched(GamePlayMovedEvent::class);
    }
}
