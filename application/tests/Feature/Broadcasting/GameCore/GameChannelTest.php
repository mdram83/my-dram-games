<?php

namespace Tests\Feature\Broadcasting\GameCore;

use App\Models\GameCore\Game\Game;
use App\Models\GameCore\Game\GameFactory;
use App\Models\GameCore\GameDefinition\GameDefinitionRepository;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GameChannelTest extends TestCase
{
    use RefreshDatabase;

    protected User $host;
    protected User $guest;
    protected Game $game;
    protected string $cookieName;
    protected bool $commonSetup = false;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->commonSetup) {

            $this->cookieName = Config::get('player.playerHashCookieName');
            $this->host = User::factory()->create();
            $this->guest = User::factory()->create();

            $gameDefinition = App::make(GameDefinitionRepository::class)->getAll()[0];;
            $this->game = App::make(GameFactory::class)->create(
                $gameDefinition->getSlug(),
                $gameDefinition->getNumberOfPlayers()[0],
                $this->host
            );

            $this->commonSetup = true;
        }
    }

    public function getResponse(
        User|false|null $user = null,
        int|string|false|null $gameId = null,
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
            'channel_name' => 'game.' . ($gameId === false ? '' : $gameId ?? $this->game->getId()),
        ]);
    }

    public function testWrongGameIdFails(): void
    {
        $response = $this->getResponse(gameId: 'definitely-not-existing-game-id');
        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testMissingGameIdFails(): void
    {
        $response = $this->getResponse(gameId: false);
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
                'slug' => $this->game->getGameDefinition()->getSlug(),
                'gameId' => $this->game->getId()
            ]));

        $cookies = $preparationResponse->headers->all()['set-cookie'];
        $hashCookie = current(array_filter($cookies, fn($cookie) => str_contains($cookie, $this->cookieName)));
        $rawCookieValue = substr($hashCookie, strlen($this->cookieName) + 1, strpos($hashCookie, ';') - strlen($this->cookieName) - 1);

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
