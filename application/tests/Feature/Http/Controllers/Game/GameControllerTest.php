<?php

namespace Tests\Feature\Http\Controllers\Game;

use App\Models\GameCore\GameDefinition\GameDefinition;
use App\Models\GameCore\GameDefinition\GameDefinitionRepository;
use App\Models\GameCore\Player\Player;
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
    protected string $route = 'ajax.play.store';
    protected string $slug;
    protected Player $player;
    protected GameDefinition $gameDefinition;

    public function setUp(): void
    {
        parent::setUp();
        if ($this->commonSetup === false) {
            $this->player = User::factory()->create();
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
    ): TestResponse
    {
        return $this
            ->actingAs($this->player, 'web')
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->post(route($this->route, [
                'slug' => $slug ?? ($nullifySlug ? null : $this->slug),
                'numberOfPlayers' => $numberOfPlayers ?? ($nullifyNumberOfPlayers ? null : $this->gameDefinition->getNumberOfPlayers()[0]),
            ]));
    }

    public function testStoreNonAjaxRequestResponseUnauthorized(): void
    {
        $response = $this
            ->actingAs($this->player, 'web')
            ->post(route($this->route, ['slug' => $this->slug]));
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testStoreNonAuthRequestResponseUnauthorized(): void
    {
        $response = $this
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->post(route($this->route, ['slug' => $this->slug]));
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testStoreThrowExceptionWithMissingSlug(): void
    {
        $this->expectException(\Exception::class);
        $this->getStoreResponse(nullifySlug: true);
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
            ->assertJsonPath('game.host.name', $this->player->getName())
            ->assertJsonPath('game.numberOfPlayers', $this->gameDefinition->getNumberOfPlayers()[0])
            ->assertJsonPath('game.players.0.name', $this->player->getName());
    }
}
