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

    protected function getResponse(
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

    public function testNonAjaxRequestResponseUnauthorized(): void
    {
        $response = $this
            ->actingAs($this->player, 'web')
            ->post(route($this->route, ['slug' => $this->slug]));
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testNonAuthRequestResponseUnauthorized(): void
    {
        $response = $this
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->post(route($this->route, ['slug' => $this->slug]));
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testThrowExceptionWithMissingSlug(): void
    {
        $this->expectException(\Exception::class);
        $this->getResponse(nullifySlug: true);
    }

    public function testBadRequestWithInconsistentSlug(): void
    {
        $response = $this->getResponse(slug: 'very-dummy-definitely-missing-slug-123');
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testBadRequestWithMissingNumberOfPlayers(): void
    {
        $response = $this->getResponse(nullifyNumberOfPlayers: true);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testBadRequestWithIncorrectNumberOfPlayers(): void
    {
        $response = $this->getResponse(numberOfPlayers: 'incorrect-value');
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testBadRequestWithInconsistentNumberOfPlayers(): void
    {
        $maxNumberOfPlayers = max($this->gameDefinition->getNumberOfPlayers());
        $response = $this->getResponse(numberOfPlayers: $maxNumberOfPlayers + 1);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testGameJsonAndHttpOkWithProperRequest(): void
    {
//        $response = $this->getResponse();
//        $response->assertStatus(Response::HTTP_OK);
        // TODO assert json has game id, owner id, numberofplayers, gameDefinition details etc. that you will need on frontend

        /* What I want to make available after request?
         * gameId (hash which is id from db
         * host name
         * number of players
         * ?gameDefinition - same as in GameDefinitionController; Maybe not needed as already available on frontend for user?
         */

    }
}
