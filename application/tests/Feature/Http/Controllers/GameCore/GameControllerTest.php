<?php

namespace Tests\Feature\Http\Controllers\GameCore;

use App\GameCore\Game\Eloquent\GameFactoryEloquent;
use App\GameCore\Game\Game;
use App\GameCore\Game\GameException;
use App\GameCore\Game\GameRepository;
use App\GameCore\GameDefinition\GameDefinition;
use App\GameCore\GameDefinition\GameDefinitionRepository;
use App\GameCore\Player\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GameControllerTest extends TestCase
{
    use RefreshDatabase;

    protected bool $commonSetup = false;
    protected string $routeStore = 'ajax.game-invites.store';
    protected string $routeJoin = 'game-invites.join';
    protected string $slug;
    protected Player $playerHost;
    protected GameDefinition $gameDefinition;

    public function setUp(): void
    {
        parent::setUp();
        if ($this->commonSetup === false) {
            $this->playerHost = User::factory()->create();
            $this->playerJoin = User::factory()->create();
            $this->slug = array_keys(Config::get('games')['gameDefinition'])[0];
            $this->gameDefinition = App::make(GameDefinitionRepository::class)->getOne($this->slug);
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
                : $this->gameDefinition->getNumberOfPlayers()[0]
        );

        $response = $this->withHeader('X-Requested-With', 'XMLHttpRequest');
        if ($auth) {
            $response = $response->actingAs($this->playerHost, 'web');
        }

        return $response->json('POST', route($this->routeStore, [ 'slug' => $slug, 'numberOfPlayers' => $numberOfPlayers,]));
    }

    protected function getJoinResponse(
        string $slug = null,
        int|string $gameId = null,
        bool $nullifySlug = false,
    ): TestResponse
    {
        return $this
            ->actingAs($this->playerJoin, 'web')
            ->get(route($this->routeJoin, [
                'slug' => $slug ?? ($nullifySlug ? null : $this->slug),
                'gameId' => $gameId,
            ]));
    }

    protected function getGame(): Game
    {
        $response = $this->getStoreResponse();
        $gameRepository = App::make(GameRepository::class);
        return $gameRepository->getOne($response['game']['id']);
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
        $maxNumberOfPlayers = max($this->gameDefinition->getNumberOfPlayers());
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

        $this->assertNotNull($response['game']['id']);
        $this->assertNotNull($response['game']['host']['name']);
        $this->assertNotNull($response['game']['numberOfPlayers']);
        $this->assertNotNull($response['game']['players'][0]['name']);

        $response
            ->assertJsonPath('game.host.name', $this->playerHost->getName())
            ->assertJsonPath('game.numberOfPlayers', $this->gameDefinition->getNumberOfPlayers()[0])
            ->assertJsonPath('game.players.0.name', $this->playerHost->getName());
    }

    public function testJoinGuestReceiveOkResponse(): void
    {
        $game = App::make(GameFactoryEloquent::class)
            ->create($this->slug, $this->gameDefinition->getNumberOfPlayers()[0], $this->playerHost);
        $gameId = $game->getId();

        $response = $this->get(route($this->routeJoin, ['slug' => $this->slug, 'gameId' => $gameId]));
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testJoinUserWithCorrectSlugWrongGameIdGetErrors(): void
    {
        $response = $this->getJoinResponse(gameId: 'whateverToTestMissingMessage');
        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirectToRoute('games.show', ['slug' => $this->slug]);
        $response->assertSessionHasErrors(['general' => GameException::MESSAGE_GAME_NOT_FOUND]);
    }

    public function testJoinGameAlreadyFullGetErrors(): void
    {
        $game = $this->getGame();
        $gameId = $game->getId();

        $numberOfPlayers = $game->getNumberOfPlayers();
        for ($i = 0; $i < $numberOfPlayers - 1; $i++) {
            $game->addPlayer(User::factory()->create());
        }

        $response = $this->getJoinResponse(gameId: $gameId);
        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirectToRoute('games.show', ['slug' => $this->slug]);
        $response->assertSessionHasErrors(['general' => GameException::MESSAGE_TOO_MANY_PLAYERS]);
    }

    public function testJoinGameWithSuccess(): void
    {
        $game = $this->getGame();
        $gameId = $game->getId();

        $response = $this->getJoinResponse(gameId: $gameId);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertViewIs('single');
        $response->assertSessionHas(['success' => \App\Http\Controllers\GameCore\GameController::MESSAGE_PLAYER_JOINED]);
        $response->assertViewHas(['game.id' => $gameId, 'game.host.name' => $game->getHost()->getName()]);
    }

    public function testJoinGameWhereUserIsAlreadyPlayerReturnSingleWithWelcomeBackMessage(): void
    {
        $game = $this->getGame();
        $game->addPlayer($this->playerJoin);

        $gameId = $game->getId();
        $response = $this->getJoinResponse(gameId: $gameId);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertViewIs('single');
        $response->assertSessionHas(['success' => \App\Http\Controllers\GameCore\GameController::MESSAGE_PLAYER_BACK]);
        $response->assertViewHas(['game.id' => $gameId, 'game.host.name' => $game->getHost()->getName()]);
    }
}
