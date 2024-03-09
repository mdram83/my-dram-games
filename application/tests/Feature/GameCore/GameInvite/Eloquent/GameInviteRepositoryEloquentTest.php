<?php

namespace Tests\Feature\GameCore\GameInvite\Eloquent;

use App\GameCore\GameInvite\GameInviteException;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameInvite\GameInviteRepository;
use App\GameCore\GameBox\GameBoxRepository;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\Player\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class GameInviteRepositoryEloquentTest extends TestCase
{
    use RefreshDatabase;

    private bool $commonSetup = false;

    private GameInviteRepository $repository;
    private GameInviteFactory $factory;

    private string $slug;
    private array $options;
    private Player $host;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->commonSetup) {

            $this->repository = App::make(GameInviteRepository::class);
            $this->factory = App::make(GameInviteFactory::class);

            $gameBoxRepository = App::make(GameBoxRepository::class);
            $gameBox = $gameBoxRepository->getOne('tic-tac-toe');

            $this->slug = $gameBox->getSlug();
            $this->options = [
                'numberOfPlayers' => GameOptionValueNumberOfPlayers::Players002,
                'autostart' => GameOptionValueAutostart::Disabled,
            ];
            $this->host = User::factory()->create();

            $this->commonSetup = true;
        }
    }

    public function testGameRepositoryCreated(): void
    {
        $this->assertInstanceOf(GameInviteRepository::class, $this->repository);
    }

    public function testIncorrectGameIdResultInException(): void
    {
        $this->expectException(GameInviteException::class);
        $this->repository->getOne('random-definitely-not-existing-game-id-123');
    }

    public function testGetOne(): void
    {
        $gameInviteId = $this->factory->create($this->slug, $this->options, $this->host)->getId();
        $gameFromRepository = $this->repository->getOne($gameInviteId);

        $this->assertEquals($gameInviteId, $gameFromRepository->getId());
    }
}
