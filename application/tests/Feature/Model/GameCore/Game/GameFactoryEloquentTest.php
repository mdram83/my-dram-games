<?php

namespace Tests\Feature\Model\GameCore\Game;

use App\Models\GameCore\Game\Game;
use App\Models\GameCore\Game\GameFactory;
use App\Models\GameCore\GameDefinition\GameDefinitionRepository;
use App\Models\GameCore\Player\Player;
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

    public function testGameCreated(): void
    {
        $factory = App::make(GameFactory::class);
        $game = $factory->create($this->slug, $this->numberOfPlayers, $this->host);

        $this->assertInstanceOf(Game::class, $game);
    }
}
