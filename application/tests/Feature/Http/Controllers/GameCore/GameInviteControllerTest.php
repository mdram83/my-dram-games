<?php

namespace Tests\Feature\Http\Controllers\GameCore;

use App\GameCore\GameInvite\Eloquent\GameInviteFactoryEloquent;
use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteException;
use App\GameCore\GameInvite\GameInviteRepository;
use App\GameCore\GameBox\GameBox;
use App\GameCore\GameBox\GameBoxRepository;
use App\GameCore\Player\Player;
use App\Http\Controllers\GameCore\GameInviteController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GameInviteControllerTest extends TestCase
{
    use RefreshDatabase;

    protected bool $commonSetup = false;
    protected string $routeStore = 'ajax.game-invites.store';
    protected string $routeJoin = 'game-invites.join';
    protected string $slug;
    protected Player $playerHost;
    protected GameBox $gameBox;

    public function setUp(): void
    {
        parent::setUp();
        if ($this->commonSetup === false) {
            $this->playerHost = User::factory()->create();
            $this->playerJoin = User::factory()->create();

            $gameBoxRepository = App::make(GameBoxRepository::class);
            $this->slug = $gameBoxRepository->getAll()[0]->getSlug();
            $this->gameBox = $gameBoxRepository->getOne($this->slug);

            $this->commonSetup = true;
        }
    }

    protected function getStoreResponse(
        string $slug = null,
        int|string $numberOfPlayers = null,
        bool $nullifySlug = false,
        bool $nullifyNumberOfPlayers = false,
        bool $auth = true,
    ): TestResponse
    {
        $slug = $slug ?? ($nullifySlug ? null : $this->slug);
        $numberOfPlayers = $numberOfPlayers ?? (
            $nullifyNumberOfPlayers
                ? null
                : $this->gameBox->getGameSetup()->getNumberOfPlayers()[0]
        );

        $response = $this->withHeader('X-Requested-With', 'XMLHttpRequest');
        if ($auth) {
            $response = $response->actingAs($this->playerHost, 'web');
        }

        return $response->json('POST', route($this->routeStore, [
            'slug' => $slug,
            'options' => [
                'numberOfPlayers' => $numberOfPlayers,
                'autostart', false,
            ],
        ]));
    }

    protected function getJoinResponse(
        string $slug = null,
        int|string $gameInviteId = null,
        bool $nullifySlug = false,
    ): TestResponse
    {
        return $this
            ->actingAs($this->playerJoin, 'web')
            ->get(route($this->routeJoin, [
                'slug' => $slug ?? ($nullifySlug ? null : $this->slug),
                'gameInviteId' => $gameInviteId,
            ]));
    }

    protected function getGameInvite(): GameInvite
    {
        $response = $this->getStoreResponse();
        $gameRepository = App::make(GameInviteRepository::class);
        return $gameRepository->getOne($response['gameInvite']['id']);
    }

    public function testStoreNonAjaxRequestResponseUnauthorized(): void
    {
        $response = $this
            ->actingAs($this->playerHost, 'web')
            ->post(route($this->routeStore, ['slug' => $this->slug]));
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testStoreGuestWebRequestResponseOk(): void
    {
        $response = $this->getStoreResponse(auth: false);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testStoreBadRequestWithMissingSlug(): void
    {
        $response = $this->getStoreResponse(nullifySlug: true);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testStoreBadRequestWithInconsistentSlug(): void
    {
        $response = $this->getStoreResponse(slug: 'very-dummy-definitely-missing-slug-123');
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testStoreBadRequestWithMissingNumberOfPlayers(): void
    {
        $response = $this->getStoreResponse(nullifyNumberOfPlayers: true);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testStoreBadRequestWithIncorrectNumberOfPlayers(): void
    {
        $response = $this->getStoreResponse(numberOfPlayers: 'incorrect-value');
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testStoreBadRequestWithInconsistentNumberOfPlayers(): void
    {
        $maxNumberOfPlayers = max($this->gameBox->getGameSetup()->getNumberOfPlayers());
        $response = $this->getStoreResponse(numberOfPlayers: $maxNumberOfPlayers + 1);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testStoreGameHttpOkWithProperRequest(): void
    {
        $response = $this->getStoreResponse();
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testStoreGameJsonCompleteWithProperRequest(): void
    {
        $response = $this->getStoreResponse();

        $this->assertNotNull($response['gameInvite']['id']);
        $this->assertNotNull($response['gameInvite']['host']['name']);
        $this->assertNotNull($response['gameInvite']['numberOfPlayers']);
        $this->assertNotNull($response['gameInvite']['players'][0]['name']);

        $response
            ->assertJsonPath('gameInvite.host.name', $this->playerHost->getName())
            ->assertJsonPath('gameInvite.numberOfPlayers', $this->gameBox->getGameSetup()->getNumberOfPlayers()[0])
            ->assertJsonPath('gameInvite.players.0.name', $this->playerHost->getName());
    }

    public function testJoinGuestReceiveOkResponse(): void
    {
        $gameInvite = App::make(GameInviteFactoryEloquent::class)
            ->create($this->slug, $this->gameBox->getGameSetup()->getNumberOfPlayers()[0], $this->playerHost);
        $gameInviteId = $gameInvite->getId();

        $response = $this->get(route($this->routeJoin, ['slug' => $this->slug, 'gameInviteId' => $gameInviteId]));
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testJoinUserWithCorrectSlugWrongGameIdGetErrors(): void
    {
        $response = $this->getJoinResponse(gameInviteId: 'whateverToTestMissingMessage');
        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirectToRoute('games.show', ['slug' => $this->slug]);
        $response->assertSessionHasErrors(['general' => GameInviteException::MESSAGE_GAME_NOT_FOUND]);
    }

    public function testJoinGameAlreadyFullGetErrors(): void
    {
        $gameInvite = $this->getGameInvite();
        $gameInviteId = $gameInvite->getId();

        $numberOfPlayers = $gameInvite->getNumberOfPlayers();
        for ($i = 0; $i < $numberOfPlayers - 1; $i++) {
            $gameInvite->addPlayer(User::factory()->create());
        }

        $response = $this->getJoinResponse(gameInviteId: $gameInviteId);
        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirectToRoute('games.show', ['slug' => $this->slug]);
        $response->assertSessionHasErrors(['general' => GameInviteException::MESSAGE_TOO_MANY_PLAYERS]);
    }

    public function testJoinGameWithSuccess(): void
    {
        $gameInvite = $this->getGameInvite();
        $gameInviteId = $gameInvite->getId();

        $response = $this->getJoinResponse(gameInviteId: $gameInviteId);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertViewIs('single');
        $response->assertSessionHas(['success' => GameInviteController::MESSAGE_PLAYER_JOINED]);
        $response->assertViewHas([
            'gameInvite.id' => $gameInviteId,
            'gameInvite.host.name' => $gameInvite->getHost()->getName()
        ]);
    }

    public function testJoinGameWhereUserIsAlreadyPlayerReturnSingleWithWelcomeBackMessage(): void
    {
        $gameInvite = $this->getGameInvite();
        $gameInvite->addPlayer($this->playerJoin);

        $gameInviteId = $gameInvite->getId();
        $response = $this->getJoinResponse(gameInviteId: $gameInviteId);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertViewIs('single');
        $response->assertSessionHas(['success' => GameInviteController::MESSAGE_PLAYER_BACK]);
        $response->assertViewHas([
            'gameInvite.id' => $gameInviteId,
            'gameInvite.host.name' => $gameInvite->getHost()->getName()
        ]);
    }
}
