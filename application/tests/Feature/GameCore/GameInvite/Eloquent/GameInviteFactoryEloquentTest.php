<?php

namespace Tests\Feature\GameCore\GameInvite\Eloquent;

use App\GameCore\GameBox\GameBoxRepository;
use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\Player\Player;
use App\GameCore\Player\PlayerAnonymousFactory;
use App\GameCore\Services\Collection\Collection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class GameInviteFactoryEloquentTest extends TestCase
{
    use RefreshDatabase;

    private Player $host;
    private string $slug;
    private CollectionGameOptionValueInput $options;

    public function setUp(): void
    {
        parent::setUp();

        $this->host = User::factory()->create();
        $gameBox = App::make(GameBoxRepository::class)->getOne('tic-tac-toe');
        $this->slug = $gameBox->getSlug();
        $this->options = new CollectionGameOptionValueInput(
            App::make(Collection::class),
            [
                'numberOfPlayers' => GameOptionValueNumberOfPlayers::Players002,
                'autostart' => GameOptionValueAutostart::Disabled,
            ]
        );
    }

    public function testGameCreatedWithUser(): void
    {
        $factory = App::make(GameInviteFactory::class);
        $gameInvite = $factory->create($this->slug, $this->options, $this->host);

        $this->assertInstanceOf(GameInvite::class, $gameInvite);
    }

    public function testGameCreatedWithGuest(): void
    {
        $factory = App::make(GameInviteFactory::class);
        $guestPlayer = App::make(PlayerAnonymousFactory::class)->create(['key' => 'test-key']);
        $gameInvite = $factory->create($this->slug, $this->options, $guestPlayer);

        $this->assertInstanceOf(GameInvite::class, $gameInvite);
    }
}
