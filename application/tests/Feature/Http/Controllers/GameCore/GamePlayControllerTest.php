<?php

namespace Tests\Feature\Http\Controllers\GameCore;

use App\Events\GameCore\GamePlay\GamePlayStartedEvent;
use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameBox\GameBoxRepository;
use App\GameCore\Player\Player;
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
    protected GameInvite $game;

    protected string $storeRouteName = 'ajax.gameplay.store';
    protected string $joinRouteName = 'gameplay.show';

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->commonSetup) {
            $this->host = User::factory()->create();
            $this->player = User::factory()->create();
            $this->notPlayer = User::factory()->create();

            $gameDefinition = App::make(GameBoxRepository::class)->getAll()[0];
            $this->game = App::make(GameInviteFactory::class)->create(
                $gameDefinition->getSlug(),
                $gameDefinition->getNumberOfPlayers()[0],
                $this->host
            );
            $this->game->addPlayer($this->player);

            $this->commonSetup = true;
        }
    }

    public function getStoreResponse(Player $player): TestResponse
    {
        return $this
            ->actingAs($player)
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->json('POST', route($this->storeRouteName, ['gameId' => $this->game->getId()]));
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
            ->json('POST', route($this->storeRouteName, ['gameId' => $this->game->getId()]));
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
            ->post(route($this->storeRouteName, ['gameId' => 'wrong-123-gg']));
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
