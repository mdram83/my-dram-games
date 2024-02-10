<?php

namespace Tests\Feature\Http\Controllers\GamePlay;

use App\Events\GameCore\GamePlay\GamePlayStartedEvent;
use App\Models\GameCore\Game\Game;
use App\Models\GameCore\Game\GameFactory;
use App\Models\GameCore\GameDefinition\GameDefinitionRepository;
use App\Models\GameCore\Player\Player;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GamePlayControllerTest extends TestCase
{
    protected bool $commonSetup = false;

    protected Player $host;
    protected Player $player;
    protected Player $notPlayer;
    protected Game $game;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->commonSetup) {
            $this->host = User::factory()->create();
            $this->player = User::factory()->create();
            $this->notPlayer = User::factory()->create();

            $gameDefinition = App::make(GameDefinitionRepository::class)->getAll()[0];
            $this->game = App::make(GameFactory::class)->create(
                $gameDefinition->getSlug(),
                $gameDefinition->getNumberOfPlayers()[0],
                $this->host
            );
            $this->game->addPlayer($this->player);

            $this->commonSetup = true;
        }
    }

    public function getResponse(Player $player): TestResponse
    {
        return $this
            ->actingAs($player)
            ->get(route('play', ['gameId' => $this->game->getId()]));
    }

    public function testGuestGetRedirectedToLoginWithRedirectUrl(): void
    {
        $response = $this->get(route('play', ['gameId' => $this->game->getId()]));
        $response->assertStatus(Response::HTTP_FOUND);
    }

    public function testNotPlayerGetForbiddenResponse(): void
    {
        $response = $this->getResponse($this->notPlayer);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testHostGetOkResponseAndStartGenerateEvent(): void
    {
        Event::fake();
        $gameId = $this->game->getId();

        $response = $this->getResponse($this->host);

        $response->assertStatus(Response::HTTP_OK);
        Event::assertDispatched(GamePlayStartedEvent::class, function($e) use ($gameId) {
            return $e->gamePlayUrl === route('play', $gameId);
        });
    }

    public function testPlayerGetOkResponseAndStartEventNotGenerated(): void
    {
        Event::fake();

        $response = $this->getResponse($this->player);
        $response->assertStatus(Response::HTTP_OK);
        Event::assertNotDispatched(GamePlayStartedEvent::class);
    }

    public function testJoinWithWrongGameId(): void
    {
        $response = $this->actingAs($this->host)->get(route('play', ['gameId' => 'wrong-123-gg']));
        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirectToRoute('home');
        $response->assertSessionHasErrors(['general' => 'Game not found']);
    }

//    public function testHostGetOkResponseAndNoEventWhenStartingSecondTime(): void
//    {
//        // TODO update later with proper functionality to start the game only once (creating GamePlay object!!!)
//    }
//
//    public function testJoiningGamePlayNotStartedByHostFails(): void
//    {
//        // TODO update later with proper functionality to start the game (creating GamePlay object!!!)
//    }
}
