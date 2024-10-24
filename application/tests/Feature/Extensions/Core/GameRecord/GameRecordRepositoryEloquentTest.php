<?php

namespace Tests\Feature\Extensions\Core\GameRecord;

use App\Extensions\Core\GameRecord\GameRecordFactoryEloquent;
use App\Extensions\Core\GameRecord\GameRecordRepositoryEloquent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use MyDramGames\Core\GameInvite\GameInvite;
use MyDramGames\Core\GameInvite\GameInviteFactory;
use MyDramGames\Core\GameOption\GameOptionConfigurationCollectionPowered;
use MyDramGames\Core\GameOption\GameOptionConfigurationGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueAutostartGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueForfeitAfterGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueNumberOfPlayersGeneric;
use MyDramGames\Core\GameRecord\GameRecordRepository;
use MyDramGames\Utils\Php\Collection\CollectionEngine;
use MyDramGames\Utils\Player\Player;
use Tests\TestCase;

class GameRecordRepositoryEloquentTest extends TestCase
{
    use RefreshDatabase;

    protected GameRecordRepositoryEloquent $repository;
    protected Player $host;
    protected Player $player;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = App::make(GameRecordRepository::class);
        $this->host = User::factory()->create();
        $this->player = User::factory()->create();
    }

    protected function getGameInvite(): GameInvite
    {
        $options = new GameOptionConfigurationCollectionPowered(
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

        $invite = App::make(GameInviteFactory::class)->create('tic-tac-toe', $options, $this->host);
        $invite->addPlayer($this->player);

        return $invite;
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(GameRecordRepository::class, $this->repository);
    }

    public function testGetReturnEmptyCollectionForInviteWithoutRecord(): void
    {
        $collection = $this->repository->getByGameInvite($this->getGameInvite());
        $this->assertEquals(0, $collection->count());
    }

    public function testGetReturnNotEmptyCollectionForInviteWithRecords(): void
    {
        $invite = $this->getGameInvite();
        $factory = App::make(GameRecordFactoryEloquent::class);
        $factory->create($invite, $this->host, true, []);
        $factory->create($invite, $this->player, false, []);
        $collection = $this->repository->getByGameInvite($invite);

        $this->assertEquals(2, $collection->count());
    }
}
