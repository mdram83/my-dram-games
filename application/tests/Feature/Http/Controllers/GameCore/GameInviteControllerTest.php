<?php

namespace Tests\Feature\Http\Controllers\GameCore;

use App\Extensions\Core\GameInvite\GameInviteFactoryEloquent;
use App\Extensions\Core\GameOption\GameOptionValueConverter;
use App\Services\PremiumPass\PremiumPassException;
use App\Http\Controllers\GameCore\GameInviteController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Testing\TestResponse;
use MyDramGames\Core\Exceptions\GameInviteException;
use MyDramGames\Core\GameBox\GameBox;
use MyDramGames\Core\GameBox\GameBoxRepository;
use MyDramGames\Core\GameInvite\GameInvite;
use MyDramGames\Core\GameInvite\GameInviteRepository;
use MyDramGames\Core\GameOption\GameOptionConfigurationCollection;
use MyDramGames\Core\GameOption\GameOptionConfigurationGeneric;
use MyDramGames\Core\GamePlay\GamePlayFactory;
use MyDramGames\Games\Thousand\Extensions\Core\GameOption\Values\GameOptionValueThousandBarrelPointsGeneric;
use MyDramGames\Games\Thousand\Extensions\Core\GameOption\Values\GameOptionValueThousandNumberOfBombsGeneric;
use MyDramGames\Games\TicTacToe\Extensions\Core\GameMoveTicTacToe;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GameInviteControllerTest extends TestCase
{
    use RefreshDatabase;

    protected string $routeStore = 'ajax.game-invites.store';
    protected string $routeJoin = 'game-invites.join';
    protected string $routeJoinRedirect = 'game-invites.join-redirect';
    protected string $slug = 'tic-tac-toe';
    protected User $playerHost;
    protected User $playerJoin;
    protected GameBox $gameBox;
    protected array $options;

    public function setUp(): void
    {
        parent::setUp();

        $this->playerHost = User::factory()->create();
        $this->playerJoin = User::factory()->create();

        $this->gameBox = App::make(GameBoxRepository::class)->getOne($this->slug);
        $this->options = ['numberOfPlayers' => 2, 'autostart' => 0, 'forfeitAfter' => 0];
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

    protected function getStoreResponsePremiumGame (
        string $slug = 'thousand',
        int|string $numberOfPlayers = 3,
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
                'thousand-barrel-points' => GameOptionValueThousandBarrelPointsGeneric::Disabled->getValue(),
                'thousand-number-of-bombs' => GameOptionValueThousandNumberOfBombsGeneric::One->getValue(),
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
            $this->gameBox->getGameSetup()->getNumberOfPlayers()->getAvailableValues()->toArray()
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
        $options = App::make(GameOptionConfigurationCollection::class);
        foreach ($this->options as $key => $value) {
            $options->add(new GameOptionConfigurationGeneric(
                $key,
                App::make(GameOptionValueConverter::class)->convert($value, $key))
            );
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
        $play = App::make(GamePlayFactory::class)->create($gameInvite);

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
        $play = App::make(GamePlayFactory::class)->create($gameInvite);

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

    public function testJoinRedirectReturnsHttpOk(): void
    {
        $gameInviteId = 'any-game-invite-id';

        $response = $this->get(route($this->routeJoinRedirect, [
            'slug' => $this->slug,
            'gameInviteId' => $gameInviteId,
        ]));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertViewHas('slug', $this->slug);
        $response->assertViewHas('gameInviteId', $gameInviteId);
    }

    public function testForbiddenWhenStoringPremiumGameWithNonPremiumHost(): void
    {
        $response = $this->getStoreResponsePremiumGame();
        $response->assertForbidden();
        $response->assertSee(PremiumPassException::MESSAGE_MISSING_PREMIUM_PASS);
    }

    public function testOkWhenStoringPremiumGameWithPremiumHost(): void
    {
        $this->playerHost->premium = true;
        $response = $this->getStoreResponsePremiumGame();
        $response->assertOk();
    }

    public function testForbiddenWhenJoiningPremiumGameWithNonPremiumPlayer(): void
    {
        $this->playerHost->premium = true;
        $storeResponse = $this->getStoreResponsePremiumGame();
        $gameInviteId = $storeResponse->json('gameInvite.id');

        $response = $this->getJoinResponse('thousand', $gameInviteId);

        $response->assertForbidden();
    }

    public function testOkWhenJoiningPremiumGameWithPremiumPlayer(): void
    {
        $this->playerHost->premium = true;
        $storeResponse = $this->getStoreResponsePremiumGame();
        $gameInviteId = $storeResponse->json('gameInvite.id');
        $this->playerJoin->premium = true;

        $response = $this->getJoinResponse('thousand', $gameInviteId);

        $response->assertOk();
    }
}
