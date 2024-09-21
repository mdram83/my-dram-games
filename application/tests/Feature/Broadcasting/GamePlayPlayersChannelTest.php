<?php

namespace Tests\Feature\Broadcasting;

use App\Broadcasting\GamePlayPlayersChannel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Testing\TestResponse;
use MyDramGames\Core\GameInvite\GameInvite;
use MyDramGames\Core\GameInvite\GameInviteFactory;
use MyDramGames\Core\GameOption\GameOptionConfigurationCollectionPowered;
use MyDramGames\Core\GameOption\GameOptionConfigurationGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueAutostartGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueForfeitAfterGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueNumberOfPlayersGeneric;
use MyDramGames\Core\GamePlay\GamePlay;
use MyDramGames\Core\GamePlay\GamePlayFactory;
use MyDramGames\Utils\Php\Collection\CollectionEngine;
use MyDramGames\Utils\Player\Player;
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
            new GameOptionConfigurationCollectionPowered(
                App::make(CollectionEngine::class),
                [
                    new GameOptionConfigurationGeneric(
                        'numberOfPlayers',
                        GameOptionValueNumberOfPlayersGeneric::Players002
                    ),
                    new GameOptionConfigurationGeneric(
                        'autostart',
                        GameOptionValueAutostartGeneric::Disabled
                    ),
                    new GameOptionConfigurationGeneric(
                        'forfeitAfter',
                        GameOptionValueForfeitAfterGeneric::Disabled
                    ),
                ]
            ),
            $this->host
        );
        $this->gameInvite->addPlayer($this->player);

        $this->gamePlay = App::make(GamePlayFactory::class)->create($this->gameInvite);
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
