<?php

namespace Tests\Feature\Extensions\Core\GameRecord;

use App\Extensions\Core\GameRecord\GameRecordFactoryEloquent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use MyDramGames\Core\Exceptions\GameRecordException;
use MyDramGames\Core\GameInvite\GameInvite;
use MyDramGames\Core\GameInvite\GameInviteFactory;
use MyDramGames\Core\GameOption\GameOptionConfigurationCollectionPowered;
use MyDramGames\Core\GameOption\GameOptionConfigurationGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueAutostartGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueForfeitAfterGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueNumberOfPlayersGeneric;
use MyDramGames\Core\GameRecord\GameRecord;
use MyDramGames\Core\GameRecord\GameRecordFactory;
use MyDramGames\Utils\Php\Collection\CollectionEngine;
use MyDramGames\Utils\Player\Player;
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
        $this->assertInstanceOf(GameRecordFactory::class, $this->factory);
    }

    public function testThrowExceptionIfCantFindInviteInDb(): void
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
        $this->assertTrue($record->isWinner());
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
