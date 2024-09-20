<?php

namespace Tests\Feature\Extensions\Core\GameInvite;

use App\Extensions\Core\GameInvite\GameInviteEloquent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use MyDramGames\Core\Exceptions\GameInviteException;
use MyDramGames\Core\GameBox\GameBoxRepository;
use MyDramGames\Core\GameInvite\GameInviteRepository;
use MyDramGames\Core\GameOption\GameOptionConfigurationCollection;
use MyDramGames\Core\GameOption\GameOptionConfigurationCollectionPowered;
use MyDramGames\Core\GameOption\GameOptionConfigurationGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueAutostartGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueForfeitAfterGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueNumberOfPlayersGeneric;
use MyDramGames\Utils\Php\Collection\CollectionEngine;
use MyDramGames\Utils\Player\Player;
use MyDramGames\Utils\Player\PlayerCollection;
use Tests\TestCase;

class GameInviteRepositoryEloquentTest extends TestCase
{
    use RefreshDatabase;

    private bool $commonSetup = false;

    private GameInviteRepository $repository;

    private string $slug;
    private GameOptionConfigurationCollection $options;
    private Player $host;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->commonSetup) {

            $this->repository = App::make(GameInviteRepository::class);

            $gameBoxRepository = App::make(GameBoxRepository::class);
            $gameBox = $gameBoxRepository->getOne('tic-tac-toe');

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
        $boxRepository = App::make(GameBoxRepository::class);

        $invite = new GameInviteEloquent(
            $boxRepository,
            App::make(PlayerCollection::class),
            App::make(GameOptionConfigurationCollection::class)
        );
        $invite->setGameBox($boxRepository->getOne('tic-tac-toe'));
        $invite->setOptions($this->options);
        $invite->addPlayer($this->host, true);

        $id = $invite->getId();
        $gameFromRepository = $this->repository->getOne($id);

        $this->assertEquals($id, $gameFromRepository->getId());
    }
}
