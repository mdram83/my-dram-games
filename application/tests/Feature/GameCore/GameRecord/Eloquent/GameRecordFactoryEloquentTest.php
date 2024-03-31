<?php

namespace Tests\Feature\GameCore\GameRecord\Eloquent;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\GameRecord\Eloquent\GameRecordFactoryEloquent;
use App\GameCore\GameRecord\GameRecord;
use App\GameCore\GameRecord\GameRecordException;
use App\GameCore\GameRecord\GameRecordFactory;
use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\Collection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class GameRecordFactoryEloquentTest extends TestCase
{
    use RefreshDatabase;

    protected GameRecordFactoryEloquent $factory;
    protected Player $host;
    protected Player $player;
    protected GameInvite $invite;
    protected array $score = ['key' => 'value'];
    protected bool $isWinner = false;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = App::make(GameRecordFactory::class);
        $this->host = User::factory()->create();
        $this->player = User::factory()->create();
        $this->invite = $this->prepareGameInvite();
    }

    protected function prepareGameInvite(): GameInvite
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
        $this->assertInstanceOf(GameRecordFactory::class, $this->factory);
    }

    public function testThrowExceptionIfCantFindIntiveInDb(): void
    {
        $this->expectException(GameRecordException::class);
        $this->expectExceptionMessage(GameRecordException::MESSAGE_MISSING_INVITE);

        $invite = $this->createMock(GameInvite::class);
        $invite->method('getId')->willReturn('test-not-existing-1234-id');

        $this->factory->create($invite, $this->host, $this->isWinner, $this->score);
    }

    public function testThrowExceptionWhenCreatingSamePlayerInviteRecordTwice(): void
    {
        $this->expectException(GameRecordException::class);
        $this->expectExceptionMessage(GameRecordException::MESSAGE_DUPLICATE_RECORD);

        $this->factory->create($this->invite, $this->host, $this->isWinner, $this->score);
        $this->factory->create($this->invite, $this->host, $this->isWinner, $this->score);
    }

    public function testCreateWinFalseTestScore(): void
    {
        $record = $this->factory->create($this->invite, $this->host, $this->isWinner, $this->score);

        $this->assertInstanceOf(GameRecord::class, $record);
        $this->assertEquals($this->isWinner, $record->isWinner());
        $this->assertEquals($this->host->getId(), $record->getPlayer()->getId());
        $this->assertEquals($this->score, $record->getScore());
    }

    public function testCreateWinTrueEmptyScore(): void
    {
        $record = $this->factory->create($this->invite, $this->host, true, []);

        $this->assertInstanceOf(GameRecord::class, $record);
        $this->assertEquals(true, $record->isWinner());
        $this->assertEquals([], $record->getScore());
    }

    public function testCreateForSameInviteDifferentPlayers(): void
    {
        $recordHost = $this->factory->create($this->invite, $this->host, true, []);
        $recordPlayer = $this->factory->create($this->invite, $this->player, false, []);

        $this->assertInstanceOf(GameRecord::class, $recordHost);
        $this->assertInstanceOf(GameRecord::class, $recordPlayer);
    }
}
