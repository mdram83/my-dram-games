<?php

namespace Tests\Feature\GameCore\Game\Eloquent;

use App\GameCore\Game\Game;
use App\GameCore\Game\GameFactory;
use App\GameCore\GameDefinition\GameDefinitionRepository;
use App\GameCore\Player\Player;
use App\GameCore\Player\PlayerAnonymousFactory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class GameFactoryEloquentTest extends TestCase
{
    use RefreshDatabase;

    private Player $host;
    private string $slug;
    private int $numberOfPlayers;

    public function setUp(): void
    {
        parent::setUp();

        $this->host = User::factory()->create();
        $this->slug = array_keys(Config::get('games')['gameDefinition'])[0];

        $gameDefinition = App::make(GameDefinitionRepository::class)->getOne($this->slug);
        $this->numberOfPlayers = $gameDefinition->getNumberOfPlayers()[0];
    }

    public function testGameCreatedWithUser(): void
    {
        $factory = App::make(GameFactory::class);
        $game = $factory->create($this->slug, $this->numberOfPlayers, $this->host);

        $this->assertInstanceOf(Game::class, $game);
    }

    public function testGameCreatedWithGuest(): void
    {
        $factory = App::make(GameFactory::class);
        $guestPlayer = App::make(PlayerAnonymousFactory::class)->create(['key' => 'test-key']);
        $game = $factory->create($this->slug, $this->numberOfPlayers, $guestPlayer);

        $this->assertInstanceOf(Game::class, $game);
    }
}
