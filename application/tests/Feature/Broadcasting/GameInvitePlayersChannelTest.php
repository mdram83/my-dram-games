<?php

namespace Tests\Feature\Broadcasting;

use App\Broadcasting\GameInvitePlayersChannel;
use App\GameCore\GameBox\GameBoxRepository;
use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\Services\Collection\Collection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GameInvitePlayersChannelTest extends TestCase
{
    use RefreshDatabase;

    protected User $host;
    protected User $guest;
    protected GameInvite $gameInvite;
    protected string $cookieName;
    protected bool $commonSetup = false;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->commonSetup) {

            $this->cookieName = Config::get('player.playerHashCookieName');
            $this->host = User::factory()->create();
            $this->guest = User::factory()->create();

            $gameBox = App::make(GameBoxRepository::class)->getOne('tic-tac-toe');
            $this->gameInvite = App::make(GameInviteFactory::class)->create(
                $gameBox->getSlug(),
                new CollectionGameOptionValueInput(
                    App::make(Collection::class),
                    [
                        'numberOfPlayers' => GameOptionValueNumberOfPlayers::Players002,
                        'autostart' => GameOptionValueAutostart::Disabled,
                    ]
                ),
                $this->host
            );

            $this->commonSetup = true;
        }
    }

    public function getResponse(
        User|false|null $user = null,
        int|string|false|null $gameInviteId = null,
        string $playerAnonymousHash = null,
    ): TestResponse
    {
        $response = $this->withHeader('X-Requested-With', 'XMLHttpRequest');

        if ($user !== false) {
            $response = $this->actingAs($user ?? $this->host, 'web');
        }

        $uri = '/broadcasting/auth';
        if ($playerAnonymousHash !== null) {
            $uri .= '?' . Config::get('player.playerHashCookieName') . '=' . $playerAnonymousHash;
        }

        return $response->post($uri, [
            'socket_id' => '1.1',
            'channel_name' =>
                GameInvitePlayersChannel::CHANNEL_ROUTE_PREFIX
                . ($gameInviteId === false ? '' : $gameInviteId ?? $this->gameInvite->getId()),
        ]);
    }

    public function testWrongGameIdFails(): void
    {
        $response = $this->getResponse(gameInviteId: 'definitely-not-existing-game-id');
        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testMissingGameIdFails(): void
    {
        $response = $this->getResponse(gameInviteId: false);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testNotGameParticipantsFails(): void
    {
        $response = $this->getResponse(user: $this->guest);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testPlayerAnonymousParticipantAuthorized(): void
    {
        $sessionId = session()->getId();
        $preparationResponse = $this
            ->withCookies([session()->getName() => $sessionId])
            ->get(route('game-invites.join', [
                'slug' => $this->gameInvite->getGameBox()->getSlug(),
                'gameInviteId' => $this->gameInvite->getId()
            ]));

        $cookies = $preparationResponse->headers->all()['set-cookie'];
        $hashCookie = current(array_filter($cookies, fn($cookie) => str_contains($cookie, $this->cookieName)));
        $rawCookieValue = substr(
            $hashCookie,
            strlen($this->cookieName) + 1,
            strpos($hashCookie, ';') - strlen($this->cookieName) - 1
        );

        $response = $this->getResponse(false, null, $rawCookieValue);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testPlayerRegisteredParticipantAuthorized(): void
    {
        $response = $this->getResponse();
        $response->assertStatus(Response::HTTP_OK);
        $response->assertSeeText($this->host->getName());
    }
}
