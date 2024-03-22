<?php

namespace Tests\Feature\Http\Controllers\GameCore;

use App\Events\GameCore\GamePlay\GamePlayStartedEvent;
use App\GameCore\GameBox\GameBoxRepository;
use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
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
    protected string $joinRouteName = 'gameplay.show';

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

    public function getStoreResponse(Player $player, GameInvite $invite): TestResponse
    {
        return $this
            ->actingAs($player)
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->json('POST', route($this->storeRouteName, ['gameInviteId' => $invite->getId()]));
    }

    public function testStoreNotPlayerGetForbiddenResponseWithNoEvent(): void
    {
        Event::fake();
        $response = $this->getStoreResponse($this->notPlayer, $this->invite);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        Event::assertNotDispatched(GamePlayStartedEvent::class);
    }

    public function testStoreGuestPlayerGetForbiddenResponseWithNoEvent(): void
    {
        Event::fake();
        $response = $this
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->json('POST', route($this->storeRouteName, ['gameInviteId' => $this->invite->getId()]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        Event::assertNotDispatched(GamePlayStartedEvent::class);
    }

    public function testStorePlayerGetForbiddenResponseWithNoEvent(): void
    {
        Event::fake();
        $response = $this->getStoreResponse($this->player, $this->invite);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        Event::assertNotDispatched(GamePlayStartedEvent::class);
    }

    public function testStoreHostGetOkResponseAndStartGenerateEvent(): void
    {
        Event::fake();
        $response = $this->getStoreResponse($this->host, $this->invite);

        $response->assertStatus(Response::HTTP_OK);
        Event::assertDispatched(GamePlayStartedEvent::class);
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
        App::make(GamePlayAbsFactoryRepository::class)->getOne('tic-tac-toe')->create($this->invite);
        $response = $this->getStoreResponse($this->host, $this->invite);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        Event::assertNotDispatched(GamePlayStartedEvent::class);
    }

    public function testStoreIncompleteInviteResultInError(): void
    {
        Event::fake();
        $response = $this->getStoreResponse($this->host, $this->prepareGameInvite(false));

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        Event::assertNotDispatched(GamePlayStartedEvent::class);
    }

    // testJoinHostGetOkResponseAndView(): void
    // testJoinPlayerGetOkResponseAndView(): void
    // testJoinNotPlayerGetForbidden(): void
    // test wrong gameplayid get error
}
