<?php

namespace Tests\Feature\Http\Controllers\GameCore;

use App\Events\GameCore\GamePlay\GamePlayDisconnectedEvent;
use App\Events\GameCore\GamePlay\GamePlayMovedEvent;
use App\Events\GameCore\GamePlay\GamePlayStoredEvent;
use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueForfeitAfter;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\GamePlay\GamePlay;
use App\GameCore\GamePlay\GamePlayAbsFactoryRepository;
use App\GameCore\GamePlayDisconnection\GamePlayDisconnectionRepository;
use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\Collection;
use App\Games\TicTacToe\GameMoveTicTacToe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GamePlayControllerTest extends TestCase
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

    public function setUp(): void
    {
        parent::setUp();

        $this->host = User::factory()->create();
        $this->player = User::factory()->create();
        $this->notPlayer = User::factory()->create();

        $this->invite = $this->prepareGameInvite();
        $this->disconnectionRepository = App::make(GamePlayDisconnectionRepository::class);
    }

    public function prepareGameInvite(bool $complete = true): GameInvite
    {
        $options = new CollectionGameOptionValueInput(
            App::make(Collection::class),
            [
                'numberOfPlayers' => GameOptionValueNumberOfPlayers::Players002,
                'autostart' => GameOptionValueAutostart::Disabled,
                'forfeitAfter' => GameOptionValueForfeitAfter::Disabled,
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
        return App::make(GamePlayAbsFactoryRepository::class)->getOne('tic-tac-toe')->create($invite);
    }

    protected function getStoreResponse(Player $player, GameInvite $invite): TestResponse
    {
        return $this
            ->actingAs($player)
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->json('POST', route($this->storeRouteName, ['gameInviteId' => $invite->getId()]));
    }

    protected function getShowResponse(Player $player, int|string $gamePlayId): TestResponse
    {
        return $this
            ->actingAs($player)
            ->get(route($this->showRouteName, ['gamePlayId' => $gamePlayId]));
    }

    protected function getMoveResponse(Player $player, int|string $gamePlayId, int $fieldKey = 1, bool|array $payload = true): TestResponse
    {
        return $this
            ->actingAs($player)
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->json(
                'POST',
                route($this->moveRouteName, ['gamePlayId' => $gamePlayId]),
                $payload === true ? ['move' => ['fieldKey' => $fieldKey]] : $payload
            );
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

    public function testStoreNotPlayerGetForbiddenResponseWithNoEvent(): void
    {
        Event::fake();
        $response = $this->getStoreResponse($this->notPlayer, $this->invite);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        Event::assertNotDispatched(GamePlayStoredEvent::class);
    }

    public function testStoreGuestPlayerGetForbiddenResponseWithNoEvent(): void
    {
        Event::fake();
        $response = $this
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->json('POST', route($this->storeRouteName, ['gameInviteId' => $this->invite->getId()]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        Event::assertNotDispatched(GamePlayStoredEvent::class);
    }

    public function testStorePlayerGetForbiddenResponseWithNoEvent(): void
    {
        Event::fake();
        $response = $this->getStoreResponse($this->player, $this->invite);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        Event::assertNotDispatched(GamePlayStoredEvent::class);
    }

    public function testStoreHostGetOkResponseAndStartGenerateEvent(): void
    {
        Event::fake();
        $response = $this->getStoreResponse($this->host, $this->invite);

        $response->assertStatus(Response::HTTP_OK);
        Event::assertDispatched(GamePlayStoredEvent::class);
    }

    public function testStoreWithWrongGameIdResultInError(): void
    {
        $response = $this
            ->actingAs($this->host)
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->post(route($this->storeRouteName, ['gameInviteId' => 'wrong-123-gg']));
        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testStoreDuplicatedInviteResultInError(): void
    {
        Event::fake();
        $this->createGamePlay($this->invite);
        $response = $this->getStoreResponse($this->host, $this->invite);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        Event::assertNotDispatched(GamePlayStoredEvent::class);
    }

    public function testStoreIncompleteInviteResultInError(): void
    {
        Event::fake();
        $response = $this->getStoreResponse($this->host, $this->prepareGameInvite(false));

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        Event::assertNotDispatched(GamePlayStoredEvent::class);
    }

    public function testShowIncorrectIdResultInError(): void
    {
        $response = $this->getShowResponse($this->host, 'missing-id-123');
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShowNotPlayerGetForbidden(): void
    {
        $play = $this->createGamePlay($this->invite);
        $response = $this->getShowResponse($this->notPlayer, $play->getId());

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testShowGetOkResponseAndContent(): void
    {
        $play = $this->createGamePlay($this->invite);
        $response = $this->getShowResponse($this->player, $play->getId());

        $response->assertStatus(Response::HTTP_OK);
        $response->assertViewIs('play');
        $response->assertViewHas(['gamePlayId' => $play->getId()]);
        $response->assertViewHas(['gameInvite' => [
            'gameInviteId' => $play->getGameInvite()->getId(),
            'slug' => $play->getGameInvite()->getGameBox()->getSlug(),
            'name' => $play->getGameInvite()->getGameBox()->getName(),
            'host' => $play->getGameInvite()->getHost()->getName(),
        ]]);
        $response->assertViewHas(['situation' => $play->getSituation($this->player)]);
    }

    public function testMoveNotPlayerGetForbidden(): void
    {
        Event::fake();
        $play = $this->createGamePlay($this->invite);
        $response = $this->getMoveResponse($this->notPlayer, $play->getId());

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        Event::assertNotDispatched(GamePlayMovedEvent::class);
    }

    public function testMoveIncorrectIdResultInErrorNoEvent(): void
    {
        Event::fake();
        $response = $this->getMoveResponse($this->notPlayer, 'some-wrong-id-123T');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        Event::assertNotDispatched(GamePlayMovedEvent::class);
    }

    public function testMoveMissingPayloadResultInErrorNoEvent(): void
    {
        Event::fake();
        $play = $this->createGamePlay($this->invite);
        $response = $this->getMoveResponse($this->host, $play->getId(), 1, []);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        Event::assertNotDispatched(GamePlayMovedEvent::class);
    }

    public function testMoveWrongPayloadResultInErrorNoEvent(): void
    {
        Event::fake();
        $play = $this->createGamePlay($this->invite);
        $response = $this->getMoveResponse($this->host, $play->getId(), 1, ['move' => ['wrong' => null]]);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        Event::assertNotDispatched(GamePlayMovedEvent::class);
    }

    public function testMoveIllegalMoveResultInErrorNoEvent(): void
    {
        Event::fake();
        $play = $this->createGamePlay($this->invite);
        $play->handleMove(new GameMoveTicTacToe($this->host, 1));
        $response = $this->getMoveResponse($this->player, $play->getId());

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        Event::assertNotDispatched(GamePlayMovedEvent::class);
    }

    public function testMoveNotPlayerTurnResultInErrorNoEvent(): void
    {
        Event::fake();
        $play = $this->createGamePlay($this->invite);
        $response = $this->getMoveResponse($this->player, $play->getId());

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        Event::assertNotDispatched(GamePlayMovedEvent::class);
    }

    public function testMoveDoneAndEventFired(): void
    {
        Event::fake();
        $play = $this->createGamePlay($this->invite);
        $response = $this->getMoveResponse($this->host, $play->getId());

        $response->assertStatus(Response::HTTP_OK);
        Event::assertDispatched(GamePlayMovedEvent::class);
    }

    public function testMoveIllegalAfterGameFinish(): void
    {
        Event::fake();
        $play = $this->createGamePlay($this->invite);
        $play->handleMove(new GameMoveTicTacToe($this->host, 1));
        $play->handleMove(new GameMoveTicTacToe($this->player, 4));
        $play->handleMove(new GameMoveTicTacToe($this->host, 2));
        $play->handleMove(new GameMoveTicTacToe($this->player, 5));
        $play->handleMove(new GameMoveTicTacToe($this->host, 3));

        $response = $this->getMoveResponse($this->player, $play->getId(), 6);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        Event::assertNotDispatched(GamePlayMovedEvent::class);
    }

    public function testShowIllegalAfterGameFinish(): void
    {
        Event::fake();
        $play = $this->createGamePlay($this->invite);
        $play->handleMove(new GameMoveTicTacToe($this->host, 1));
        $play->handleMove(new GameMoveTicTacToe($this->player, 4));
        $play->handleMove(new GameMoveTicTacToe($this->host, 2));
        $play->handleMove(new GameMoveTicTacToe($this->player, 5));
        $play->handleMove(new GameMoveTicTacToe($this->host, 3));

        $response = $this->getShowResponse($this->player, $play->getId());

        $response->assertStatus(Response::HTTP_FOUND);
        Event::assertNotDispatched(GamePlayMovedEvent::class);
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
}
