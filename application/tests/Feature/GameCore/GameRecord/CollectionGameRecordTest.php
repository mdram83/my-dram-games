<?php

namespace Tests\Feature\GameCore\GameRecord;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\GameRecord\CollectionGameRecord;
use App\GameCore\GameRecord\GameRecord;
use App\GameCore\GameRecord\GameRecordFactory;
use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\Collection;
use App\GameCore\Services\Collection\CollectionException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class CollectionGameRecordTest extends TestCase
{
    use RefreshDatabase;

    protected Collection $handler;
    protected GameRecord $recordOne;
    protected GameRecord $recordTwo;

    public function setUp(): void
    {
        parent::setUp();
        $this->handler = App::make(Collection::class);
        $host = User::factory()->create();
        $player = User::factory()->create();
        $invite = $this->prepareGameInvite($host, $player);
        $factory = App::make(GameRecordFactory::class);

        $this->recordOne = $factory->create($invite, $host, true, []);
        $this->recordTwo = $factory->create($invite, $player, false, []);
    }

    protected function prepareGameInvite(Player $host, Player $player): GameInvite
    {
        $options = new CollectionGameOptionValueInput(
            App::make(Collection::class),
            [
                'numberOfPlayers' => GameOptionValueNumberOfPlayers::Players002,
                'autostart' => GameOptionValueAutostart::Disabled,
            ]
        );

        $invite = App::make(GameInviteFactory::class)->create('tic-tac-toe', $options, $host);
        $invite->addPlayer($player);

        return $invite;
    }

    public function testInstance(): void
    {
        $collection = new CollectionGameRecord($this->handler);
        $this->assertInstanceOf(CollectionGameRecord::class, $collection);
        $this->assertInstanceOf(Collection::class, $collection);
    }

    public function testThrowExceptionWhenAddingIncompatibleObjectInNew(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_INCOMPATIBLE);

        new CollectionGameRecord($this->handler, ['1']);
    }

    public function testThrowExceptionWhenAddingIncompatibleObjectInAdd(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_INCOMPATIBLE);

        (new CollectionGameRecord($this->handler))->add('1');
    }

    public function testCreateSameObjectTwiceIsOk(): void
    {
        $collection = new CollectionGameRecord($this->handler, [$this->recordOne, $this->recordOne]);
        $this->assertEquals(2, $collection->count());
    }

    public function testAddDifferentObjectsOk(): void
    {
        $collection = new CollectionGameRecord($this->handler);
        $collection->add($this->recordOne);
        $collection->add($this->recordTwo);

        $this->assertEquals(2, $collection->count());
    }
}
