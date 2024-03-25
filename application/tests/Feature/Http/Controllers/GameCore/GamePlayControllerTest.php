<?php

namespace Tests\Feature\Http\Controllers\GameCore;

use App\Events\GameCore\GamePlay\GamePlayStoredEvent;
use App\GameCore\GameBox\GameBoxRepository;
use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\GamePlay\GamePlay;
use App\GameCore\GamePlay\GamePlayAbsFactoryRepository;
use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\Collection;
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

    protected string $storeRouteName = 'ajax.gameplay.store';
    protected string $showRouteName = 'gameplay.show';
    protected string $moveRouteName = 'ajax.gameplay.move';

    public function setUp(): void
    {
        parent::setUp();

        $this->host = User::factory()->create();
        $this->player = User::factory()->create();
        $this->notPlayer = User::factory()->create();

        $this->invite = $this->prepareGameInvite();
    }

    public function prepareGameInvite(bool $complete = true): GameInvite
    {
        $options = new CollectionGameOptionValueInput(
            App::make(Collection::class),
            [
                'numberOfPlayers' => GameOptionValueNumberOfPlayers::Players002,
                'autostart' => GameOptionValueAutostart::Disabled,
            ]
        );

        $invite = App::make(GameInviteFactory::class)->create('tic-tac-toe', $options, $this->host);

        if ($complete) {
            $invite->addPlayer($this->player);
        }

        return $invite;
    }

    public function createGamePlay(GameInvite $invite): GamePlay
    {
        return App::make(GamePlayAbsFactoryRepository::class)->getOne('tic-tac-toe')->create($invite);
    }

    public function getStoreResponse(Player $player, GameInvite $invite): TestResponse
    {
        return $this
            ->actingAs($player)
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->json('POST', route($this->storeRouteName, ['gameInviteId' => $invite->getId()]));
    }

    public function getShowResponse(Player $player, int|string $gamePlayId): TestResponse
    {
        return $this
            ->actingAs($player)
            ->get(route($this->showRouteName, ['gamePlayId' => $gamePlayId]));
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

    // NEXT TESTS FOR MOVES
    // not player forbidden no event
    // wrong game id is error
    // missing payload input is error
    // wrong payload input is error (validation)
    // wrong move is error
    // wrong (not your turn) player is error
    // move done and events fired (all players)

    // LATER HANDLE WIN SITUATION AND FOLLOWING MOVES (BASICALLY FORBIDDEN)
}
