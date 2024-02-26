<?php

namespace Tests\Feature\GameCore\GameInvite\Eloquent;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameBox\GameBoxRepository;
use App\GameCore\Player\Player;
use App\GameCore\Player\PlayerAnonymousFactory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class GameInviteFactoryEloquentTest extends TestCase
{
    use RefreshDatabase;

    private Player $host;
    private string $slug;
    private array $options;

    public function setUp(): void
    {
        parent::setUp();

        $this->host = User::factory()->create();
        $gameBox = App::make(GameBoxRepository::class)->getAll()[0];
        $this->slug = $gameBox->getSlug();
        $this->options = ['numberOfPlayers' => $gameBox->getGameSetup()->getNumberOfPlayers()[0], 'autostart' => false];
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
