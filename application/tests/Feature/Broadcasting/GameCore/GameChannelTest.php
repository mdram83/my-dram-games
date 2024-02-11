<?php

namespace Tests\Feature\Broadcasting\GameCore;

use App\Models\GameCore\Game\Game;
use App\Models\GameCore\Game\GameFactory;
use App\Models\GameCore\GameDefinition\GameDefinitionRepository;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GameChannelTest extends TestCase
{
    protected User $host;
    protected User $guest;
    protected Game $game;
    protected bool $commonSetup = false;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->commonSetup) {
            $this->host = User::factory()->create();
            $this->guest = User::factory()->create();

            $slug = array_keys(Config::get('games')['gameDefinition'])[0];
            $gameDefinition = App::make(GameDefinitionRepository::class)->getOne($slug);
            $numberOfPlayers = $gameDefinition->getNumberOfPlayers()[0];

            $factory = App::make(GameFactory::class);
            $this->game = $factory->create($slug, $numberOfPlayers, $this->host);

            $this->commonSetup = true;
        }
    }

    public function getResponse(
        User|false|null $user = null,
        int|string|false|null $gameId = null
    ): TestResponse
    {
        $response = $this->withHeader('X-Requested-With', 'XMLHttpRequest');

        if ($user !== false) {
            $response = $this->actingAs($user ?? $this->host, 'web');
        }

        return $response->post('/broadcasting/auth', [
            'socket_id' => '1.1',
            'channel_name' => 'game.' . ($gameId === false ? '' : $gameId ?? $this->game->getId()),
        ]);
    }

    public function testUnauthorizedRequestFails(): void
    {
        $response = $this->getResponse(user: false);
        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
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

    public function testPlayerAuthorized(): void
    {
        $response = $this->getResponse();
        $response->assertStatus(Response::HTTP_OK);
    }

}
