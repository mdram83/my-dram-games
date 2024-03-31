<?php

namespace Tests\Feature\GameCore\GameRecord\Eloquent;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\GameRecord\Eloquent\GameRecordFactoryEloquent;
use App\GameCore\GameRecord\Eloquent\GameRecordRepositoryEloquent;
use App\GameCore\GameRecord\GameRecordFactory;
use App\GameCore\GameRecord\GameRecordRepository;
use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\Collection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
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
        $options = new CollectionGameOptionValueInput(
            App::make(Collection::class),
            [
                'numberOfPlayers' => GameOptionValueNumberOfPlayers::Players002,
                'autostart' => GameOptionValueAutostart::Disabled,
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
