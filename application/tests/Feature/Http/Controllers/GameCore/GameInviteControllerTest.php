<?php

namespace Tests\Feature\Http\Controllers\GameCore;

use App\GameCore\GameBox\GameBox;
use App\GameCore\GameBox\GameBoxRepository;
use App\GameCore\GameInvite\Eloquent\GameInviteFactoryEloquent;
use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteException;
use App\GameCore\GameInvite\GameInviteRepository;
use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\GameOptionValue\GameOptionValueConverter;
use App\GameCore\GamePlay\GamePlayAbsFactoryRepository;
use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\Collection;
use App\Games\TicTacToe\GameMoveTicTacToe;
use App\Http\Controllers\GameCore\GameInviteController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GameInviteControllerTest extends TestCase
{
    use RefreshDatabase;

    protected bool $commonSetup = false;
    protected string $routeStore = 'ajax.game-invites.store';
    protected string $routeJoin = 'game-invites.join';
    protected string $slug = 'tic-tac-toe';
    protected Player $playerHost;
    protected GameBox $gameBox;
    protected array $options;

    public function setUp(): void
    {
        parent::setUp();
        if ($this->commonSetup === false) {

            $this->playerHost = User::factory()->create();
            $this->playerJoin = User::factory()->create();

            $this->gameBox = App::make(GameBoxRepository::class)->getOne($this->slug);
            $this->options = ['numberOfPlayers' => 2, 'autostart' => 0, 'forfeitAfter' => 0];

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
                : $this->options['numberOfPlayers']
        );

        $response = $this->withHeader('X-Requested-With', 'XMLHttpRequest');
        if ($auth) {
            $response = $response->actingAs($this->playerHost, 'web');
        }

        return $response->json('POST', route($this->routeStore, [
            'slug' => $slug,
            'options' => [
                'numberOfPlayers' => $numberOfPlayers,
                'autostart' => $this->options['autostart'],
                'forfeitAfter' => '0',
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
        $maxNumberOfPlayers = max(array_map(
            fn($optionValue) => $optionValue->value,
            $this->gameBox->getGameSetup()->getNumberOfPlayers()->getAvailableValues()
        ));
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
        $this->assertNotNull($response['gameInvite']['options']['numberOfPlayers']);
        $this->assertNotNull($response['gameInvite']['options']['autostart']);
        $this->assertNotNull($response['gameInvite']['players'][0]['name']);

        $response
            ->assertJsonPath('gameInvite.host.name', $this->playerHost->getName())
            ->assertJsonPath('gameInvite.options.numberOfPlayers', $this->options['numberOfPlayers'])
            ->assertJsonPath('gameInvite.options.autostart', $this->options['autostart'])
            ->assertJsonPath('gameInvite.players.0.name', $this->playerHost->getName());
    }

    public function testJoinGuestReceiveOkResponse(): void
    {
        $options = new CollectionGameOptionValueInput(App::make(Collection::class));
        foreach ($this->options as $key => $value) {
            $options->add(App::make(GameOptionValueConverter::class)->convert($value, $key), $key);
        }

        $gameInvite = App::make(GameInviteFactoryEloquent::class)
            ->create($this->slug, $options, $this->playerHost);
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

        $numberOfPlayers = $gameInvite->getGameSetup()->getNumberOfPlayers()->getConfiguredValue()->getValue();
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
            'gameInvite.host.name' => $gameInvite->getHost()->getName(),
            'gamePlayId' => null,
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
            'gameInvite.host.name' => $gameInvite->getHost()->getName(),
            'gamePlayId' => null,
        ]);
    }

    public function testJoinGameWhereUserIsAlreadyPlayerAndGameStartedReturnSingleWithWelcomeBackMessage(): void
    {
        $gameInvite = $this->getGameInvite();
        $gameInvite->addPlayer($this->playerJoin);
        $factory = App::make(GamePlayAbsFactoryRepository::class)->getOne($gameInvite->getGameBox()->getSlug());
        $play = $factory->create($gameInvite);

        $gameInviteId = $gameInvite->getId();
        $response = $this->getJoinResponse(gameInviteId: $gameInviteId);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertViewIs('single');
        $response->assertSessionHas(['success' => GameInviteController::MESSAGE_PLAYER_BACK]);
        $response->assertViewHas([
            'gameInvite.id' => $gameInviteId,
            'gameInvite.host.name' => $gameInvite->getHost()->getName(),
            'gamePlayId' => $play->getId(),
        ]);
    }

    public function testJoinFinishedGameForPlayerResultInExposingGameRecords(): void
    {
        $gameInvite = $this->getGameInvite();
        $gameInvite->addPlayer($this->playerJoin);
        $factory = App::make(GamePlayAbsFactoryRepository::class)->getOne($gameInvite->getGameBox()->getSlug());
        $play = $factory->create($gameInvite);
        $play->handleMove(new GameMoveTicTacToe($this->playerHost, 1));
        $play->handleMove(new GameMoveTicTacToe($this->playerJoin, 4));
        $play->handleMove(new GameMoveTicTacToe($this->playerHost, 2));
        $play->handleMove(new GameMoveTicTacToe($this->playerJoin, 5));
        $play->handleMove(new GameMoveTicTacToe($this->playerHost, 3));

        $gameInviteId = $gameInvite->getId();
        $response = $this->getJoinResponse(gameInviteId: $gameInviteId);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertViewIs('single');
        $response->assertSessionHas(['success' => GameInviteController::MESSAGE_PLAYER_BACK]);
        $response->assertViewHas([
            'gameInvite.id' => $gameInviteId,
            'gameInvite.host.name' => $gameInvite->getHost()->getName(),
            'gamePlayId' => $play->getId(),
        ]);
        $response->assertViewHas(['gameRecords']);
    }
}
