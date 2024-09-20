<?php

namespace Tests\Feature\Extensions\Core\GameInvite;

use App\GameCore\Player\PlayerAnonymousFactory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use MyDramGames\Core\GameBox\GameBoxRepository;
use MyDramGames\Core\GameInvite\GameInvite;
use MyDramGames\Core\GameInvite\GameInviteFactory;
use MyDramGames\Core\GameOption\GameOptionConfigurationCollection;
use MyDramGames\Core\GameOption\GameOptionConfigurationCollectionPowered;
use MyDramGames\Core\GameOption\GameOptionConfigurationGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueAutostartGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueForfeitAfterGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueNumberOfPlayersGeneric;
use MyDramGames\Utils\Php\Collection\CollectionEngine;
use MyDramGames\Utils\Player\Player;
use Tests\TestCase;

class GameInviteFactoryEloquentTest extends TestCase
{
    use RefreshDatabase;

    private Player $host;
    private string $slug;
    private GameOptionConfigurationCollection $options;

    public function setUp(): void
    {
        parent::setUp();

        $this->host = User::factory()->create();
        $gameBox = App::make(GameBoxRepository::class)->getOne('tic-tac-toe');
        $this->slug = $gameBox->getSlug();
        $this->options = new GameOptionConfigurationCollectionPowered(
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
