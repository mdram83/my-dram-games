<?php

namespace Tests\Feature\Broadcasting;

use App\Broadcasting\GamePlayPlayerChannel;
use App\Broadcasting\GamePlayPlayersChannel;
use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\GamePlay\GamePlay;
use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\Collection;
use App\Games\TicTacToe\GamePlayAbsFactoryTicTacToe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GamePlayPlayersChannelTest extends TestCase
{
    use RefreshDatabase;

    protected User $host;
    protected Player $player;
    protected User $guest;
    protected GameInvite $gameInvite;
    protected GamePlay $gamePlay;
    protected string $cookieName;

    public function setUp(): void
    {
        parent::setUp();

        $this->cookieName = Config::get('player.playerHashCookieName');
        $this->host = User::factory()->create();
        $this->player = User::factory()->create();
        $this->guest = User::factory()->create();

        $this->gameInvite = App::make(GameInviteFactory::class)->create(
            'tic-tac-toe',
            new CollectionGameOptionValueInput(
                App::make(Collection::class),
                [
                    'numberOfPlayers' => GameOptionValueNumberOfPlayers::Players002,
                    'autostart' => GameOptionValueAutostart::Disabled,
                ]
            ),
            $this->host
        );
        $this->gameInvite->addPlayer($this->player);

        $this->gamePlay = App::make(GamePlayAbsFactoryTicTacToe::class)->create($this->gameInvite);
    }

    public function getResponse(
        User|false|null $user = null,
        int|string|false|null $gamePlayId = null,
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
                GamePlayPlayersChannel::CHANNEL_ROUTE_PREFIX
                . ($gamePlayId === false ? '' : $gamePlayId ?? $this->gamePlay->getId()),
        ]);
    }

    public function testWrongGameIdFails(): void
    {
        $response = $this->getResponse(gamePlayId: 'definitely-not-existing-game-id');
        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testMissingGameIdFails(): void
    {
        $response = $this->getResponse(gamePlayId: false);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testNotGameParticipantsFails(): void
    {
        $response = $this->getResponse(user: $this->guest);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testPlayerRegisteredParticipantAuthorized(): void
    {
        $response = $this->getResponse();
        $response->assertStatus(Response::HTTP_OK);
    }
}
