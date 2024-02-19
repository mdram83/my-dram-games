<?php

namespace Tests\Feature\Http\Middleware;

use App\Models\GameCore\GameDefinition\GameDefinitionRepository;
use App\Models\GameCore\Player\PlayerAnonymous;
use App\Models\GameCore\Player\PlayerAnonymousEloquent;
use App\Models\GameCore\Player\PlayerAnonymousRepository;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class PlayerMiddlewareTest extends TestCase
{
    protected string $cookieName;
    protected string $slug;
    protected PlayerAnonymousRepository $repository;
    protected bool $commonSetup = false;

    public function setUp(): void
    {
        parent::setUp();
        if (!$this->commonSetup) {
            $this->cookieName = Config::get('player.playerHashCookieName');
            $this->slug = App::make(GameDefinitionRepository::class)->getAll()[0]->getSlug();
            $this->repository = App::make(PlayerAnonymousRepository::class);
            $this->commonSetup = true;
        }
    }

    public function testWithoutPlayerMiddlewareDoesNotCreatePlayerHashCookie(): void
    {
        $response = $this->get(route('home'));
        $response->assertCookieMissing($this->cookieName);
    }

    public function testGuestFirstRequestCreatePlayerAnonymousHashWithCookie(): void
    {
        $response = $this->get(route('games.show', $this->slug));
        $cookie = $response->getCookie($this->cookieName);
        $this->assertNotNull($cookie);
        $this->assertInstanceOf(PlayerAnonymous::class, $this->repository->getOne($cookie->getValue()));
    }

    public function testGuestSecondRequestReturnsExistingPlayerAnonymousHashExtendingCookieAndUpdatedOn(): void
    {
        $sessionId = session()->getId();

        $response = $this->withCookies([session()->getName() => $sessionId])->get(route('games.show', $this->slug));
        $cookie = $response->getCookie($this->cookieName);
        $hash = $cookie->getValue();
        $player = $this->repository->getOne($hash);

        $this->travel(Config::get('player.playerHashExpiration') / 2)->minutes();

        $responseTwo = $this->withCookies([session()->getName() => $sessionId])->get(route('games.show', $this->slug));
        $cookieTwo = $responseTwo->getCookie($this->cookieName);
        $hashTwo = $cookieTwo->getValue();
        $playerTwo = $this->repository->getOne($hashTwo);

        $this->assertEquals($hash, $hashTwo);
        $this->assertEquals($player->getId(), $playerTwo->getId());
        $this->assertGreaterThanOrEqual($cookie->getExpiresTime(), $cookieTwo->getExpiresTime());
    }

    public function testAuthRequestDestroyPlayerHashCookie(): void
    {
        $this->get(route('games.show', $this->slug));
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'web')->get(route('games.show', $this->slug));
        $response->assertCookieExpired($this->cookieName);
    }
}
