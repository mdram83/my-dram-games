<?php

namespace Tests\Feature\GameCore\GameInvite\Eloquent;

use App\GameCore\GameInvite\GameInviteException;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameInvite\GameInviteRepository;
use App\GameCore\GameBox\GameBoxRepository;
use App\GameCore\Player\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class GameInviteRepositoryEloquentTest extends TestCase
{
    use RefreshDatabase;

    private bool $commonSetup = false;

    private \App\GameCore\GameInvite\GameInviteRepository $repository;
    private GameInviteFactory $factory;

    private string $slug;
    private int $numberOfPlayers;
    private Player $host;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->commonSetup) {

            $this->repository = App::make(GameInviteRepository::class);
            $this->factory = App::make(GameInviteFactory::class);

            $gameDefinitionRepository = App::make(GameBoxRepository::class);
            $gameDefinition = $gameDefinitionRepository->getAll()[0];

            $this->slug = $gameDefinition->getSlug();
            $this->numberOfPlayers = $gameDefinition->getNumberOfPlayers()[0];
            $this->host = User::factory()->create();

            $this->commonSetup = true;
        }
    }

    public function testGameRepositoryCreated(): void
    {
        $this->assertInstanceOf(\App\GameCore\GameInvite\GameInviteRepository::class, $this->repository);
    }

    public function testIncorrectGameIdResultInException(): void
    {
        $this->expectException(GameInviteException::class);
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
