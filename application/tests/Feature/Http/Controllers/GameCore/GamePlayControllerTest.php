<?php

namespace Tests\Feature\Http\Controllers\GameCore;

use App\Events\GameCore\GamePlay\GamePlayStartedEvent;
use App\GameCore\GameBox\GameBoxRepository;
use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
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

    protected bool $commonSetup = false;

    protected Player $host;
    protected Player $player;
    protected Player $notPlayer;
    protected GameInvite $gameInvite;

    protected string $storeRouteName = 'ajax.gameplay.store';
    protected string $joinRouteName = 'gameplay.show';

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->commonSetup) {
            $this->host = User::factory()->create();
            $this->player = User::factory()->create();
            $this->notPlayer = User::factory()->create();

            $gameBox = App::make(GameBoxRepository::class)->getOne('tic-tac-toe');

            $options = new CollectionGameOptionValueInput(
                App::make(Collection::class),
                [
                    'numberOfPlayers' => GameOptionValueNumberOfPlayers::Players002,
                    'autostart' => GameOptionValueAutostart::Disabled,
                ]
            );

            $this->gameInvite = App::make(GameInviteFactory::class)->create(
                $gameBox->getSlug(),
                $options,
                $this->host
            );
            $this->gameInvite->addPlayer($this->player);

            $this->commonSetup = true;
        }
    }

    public function getStoreResponse(Player $player): TestResponse
    {
        return $this
            ->actingAs($player)
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->json('POST', route($this->storeRouteName, ['gameInviteId' => $this->gameInvite->getId()]));
    }

    public function testStoreNotPlayerGetForbiddenResponseWithNoEvent(): void
    {
        Event::fake();
        $response = $this->getStoreResponse($this->notPlayer);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        Event::assertNotDispatched(GamePlayStartedEvent::class);
    }

    public function testStoreGuestPlayerGetForbiddenResponseWithNoEvent(): void
    {
        Event::fake();
        $response = $this
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->json('POST', route($this->storeRouteName, ['gameInviteId' => $this->gameInvite->getId()]));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        Event::assertNotDispatched(GamePlayStartedEvent::class);
    }

    public function testStorePlayerGetForbiddenResponseWithNoEvent(): void
    {
        Event::fake();
        $response = $this->getStoreResponse($this->player);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        Event::assertNotDispatched(GamePlayStartedEvent::class);
    }

    public function testStoreHostGetOkResponseAndStartGenerateEvent(): void
    {
        Event::fake();
        $response = $this->getStoreResponse($this->host);
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

//    public function testStoreHostGetOkResponseAndNoEventWhenStartingSecondTime(): void
//    {
//        // TODO update later with proper functionality to start the game only once (creating GamePlay object!!!)
//    }
//
//    public function testStoreJoiningGamePlayNotStartedByHostFails(): void
//    {
//        // TODO update later with proper functionality to start the game (creating GamePlay object!!!)
//    }
//
//    public function testJoinHostGetOkResponseAndView(): void
//    {
//
//    }
//
//    public function testJoinPlayerGetOkResponseAndView(): void
//    {
//
//    }
//
//    public function testJoinNotPlayerGetForbidden(): void
//    {
//
//    }
}
