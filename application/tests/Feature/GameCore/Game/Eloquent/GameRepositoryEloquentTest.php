<?php

namespace Tests\Feature\GameCore\Game\Eloquent;

use App\GameCore\Game\GameException;
use App\GameCore\Game\GameFactory;
use App\GameCore\Game\GameRepository;
use App\GameCore\GameDefinition\GameDefinitionRepository;
use App\GameCore\Player\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class GameRepositoryEloquentTest extends TestCase
{
    use RefreshDatabase;

    private bool $commonSetup = false;

    private \App\GameCore\Game\GameRepository $repository;
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

            $this->commonSetup = true;
        }
    }

    public function testGameRepositoryCreated(): void
    {
        $this->assertInstanceOf(\App\GameCore\Game\GameRepository::class, $this->repository);
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
