<?php

namespace Tests\Feature\Model\GameCore\Game;

use App\Models\GameCore\Game\Game;
use App\Models\GameCore\Game\GameException;
use App\Models\GameCore\Game\GameFactory;
use App\Models\GameCore\Game\GameRepository;
use App\Models\GameCore\Game\GameRepositoryEloquent;
use App\Models\GameCore\GameDefinition\GameDefinitionRepository;
use App\Models\GameCore\Player\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class GameRepositoryEloquentTest extends TestCase
{
    use RefreshDatabase;

    private bool $commonSetup = false;

    private GameRepository $repository;
    private GameFactory $factory;

    private string $slug;
    private int $numberOfPlayers;
    private Player $host;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->commonSetup) {

            $this->repository = App::make(GameRepository::class);
            $this->factory = App::make(GameFactory::class);

            $gameDefinitionRepository = App::make(GameDefinitionRepository::class);
            $gameDefinition = $gameDefinitionRepository->getAll()[0];

            $this->slug = $gameDefinition->getSlug();
            $this->numberOfPlayers = $gameDefinition->getNumberOfPlayers()[0];
            $this->host = User::factory()->create();
        }
    }

    public function testGameRepositoryCreated(): void
    {
        $this->assertInstanceOf(GameRepository::class, $this->repository);
    }

    public function testIncorrectGameIdResultInException(): void
    {
        $this->expectException(GameException::class);
        $this->repository->getOne('random-definitely-not-existing-game-id-123');
    }

    public function testGetOne(): void
    {
        $game = $this->factory->create($this->slug, $this->numberOfPlayers, $this->host);
        $gameId = $game->getId();
        $gameFromRepository = $this->repository->getOne($gameId);

        $this->assertEquals($gameId, $gameFromRepository->getId());
    }
}
