<?php

namespace Tests\Feature\GameCore\GameInvite\Eloquent;

use App\GameCore\GameInvite\Eloquent\GameInviteEloquent;
use App\GameCore\GameInvite\GameInviteException;
use App\GameCore\GameBox\GameBox;
use App\GameCore\GameBox\GameBoxRepository;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\GameSetup\GameSetup;
use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\Collection;
use App\GameCore\Services\Collection\CollectionGameOptionValueInput;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class GameInviteEloquentTest extends TestCase
{
    use RefreshDatabase;

    protected GameInviteEloquent $gameInvite;
    protected Player $playerOne;
    protected Player $playerTwo;
    protected GameBox $gameBox;
    protected CollectionGameOptionValueInput $options;

    public function setUp(): void
    {
        parent::setUp();

        $this->playerOne = User::factory()->create();
        $this->playerTwo = User::factory()->create();

        $this->options = new CollectionGameOptionValueInput( // added
            App::make(Collection::class),
            [
                'numberOfPlayers' => GameOptionValueNumberOfPlayers::Players002,
                'autostart' => GameOptionValueAutostart::Disabled,
            ]
        );

        $this->gameBox = App::make(GameBoxRepository::class)->getOne('tic-tac-toe');
        $this->gameInvite = new GameInviteEloquent(
            App::make(GameBoxRepository::class),
            App::make(Collection::class)
        );
    }

    protected function fullConfig(): void
    {
        $this->gameInvite->setGameBox($this->gameBox);
        $this->gameInvite->setOptions($this->options);
    }

    public function testGameEloquentObjectCreated(): void
    {
        $this->assertInstanceOf(GameInviteEloquent::class, $this->gameInvite);
    }

    public function testGameIdAvailableUponGameCreation(): void
    {
        $this->assertNotNull($this->gameInvite->getId());
    }

    // Setting/Getting GameBox

    public function testThrowExceptionWhenOverwritingGameBox(): void
    {
        $this->expectException(GameInviteException::class);
        $this->gameInvite->setGameBox($this->gameBox);
        $this->gameInvite->setGameBox($this->gameBox);
    }

    public function testGameBoxSetAndReturned(): void
    {
        $this->gameInvite->setGameBox($this->gameBox);
        $this->assertSame($this->gameBox, $this->gameInvite->getGameBox());
    }

    public function testThrowExceptionWhenGettingUnsetGameBox(): void
    {
        $this->expectException(GameInviteException::class);
        $this->gameInvite->getGameBox();
    }

    // Setting/Getting Number Of Players

    public function testThrowExceptionWhenGettingGameSetupWitoutSettingGameBox(): void
    {
        $this->expectException(GameInviteException::class);
        $this->gameInvite->getGameSetup();
    }

    public function testThrowExceptionWhenSettingOptionsWithoutSettingGameBox(): void
    {
        $this->expectException(GameInviteException::class);
        $this->gameInvite->setOptions($this->options);
    }

    public function testThrowExceptionWhenSettingOptionsTwice(): void
    {
        $this->expectException(GameInviteException::class);

        $this->gameInvite->setGameBox($this->gameBox);
        $this->gameInvite->setOptions($this->options);
        $this->gameInvite->setOptions($this->options);
    }

    public function testSetOptionsAndGetGameSetup(): void
    {
        $this->fullConfig();
        $this->assertInstanceOf(GameSetup::class, $this->gameInvite->getGameSetup());
    }

    public function testGetGameSetupFromLoadedObject(): void
    {
        $this->fullConfig();
        $id = $this->gameInvite->getId();

        $invite = new GameInviteEloquent(
            App::make(GameBoxRepository::class),
            App::make(Collection::class),
            $id
        );
        $this->assertInstanceOf(GameSetup::class, $invite->getGameSetup());
    }

    // Setting/Getting Players

    public function testThrowExceptionWhenAddingPlayerWithoutSettingOptions(): void
    {
        $this->expectException(GameInviteException::class);

        $this->gameInvite->setGameBox($this->gameBox);
        $this->gameInvite->addPlayer($this->playerOne);
    }

    public function testThrowExceptionWhenAddingTooManyPlayers(): void
    {
        $this->expectException(GameInviteException::class);

        $this->fullConfig();

        $playerThree = $this->createMock(Player::class);
        $playerThree->method('getId')->willReturn(3);

        $this->gameInvite->addPlayer($this->playerOne, true);
        $this->gameInvite->addPlayer($this->playerTwo);
        $this->gameInvite->addPlayer($playerThree);
    }

    public function testThrowExceptionWhenAddingSamePlayerManyTimes(): void
    {
        $this->expectException(GameInviteException::class);

        $this->fullConfig();
        $this->gameInvite->addPlayer($this->playerOne);
        $this->gameInvite->addPlayer($this->playerOne);
    }

    public function testThrowExceptionWhenAddingHostMoreThenOnce(): void
    {
        $this->expectException(GameInviteException::class);

        $this->fullConfig();
        $this->gameInvite->addPlayer($this->playerOne, true);
        $this->gameInvite->addPlayer($this->playerTwo, true);
    }

    public function testThrowExceptionWhenAddingPlayerToGameWithoutHost(): void
    {
        $this->expectException(GameInviteException::class);

        $this->fullConfig();
        $this->gameInvite->addPlayer($this->playerOne);
        $this->gameInvite->addPlayer($this->playerTwo);
    }

    public function testGameHostAddedAndReturned(): void
    {
        $this->fullConfig();
        $this->gameInvite->addPlayer($this->playerOne, true);

        $this->assertEquals($this->playerOne->getId(), $this->gameInvite->getHost()->getId());
    }

    public function testGameHostAddedAndBecomesAlsoOneOfPlayers(): void
    {
        $this->fullConfig();
        $this->gameInvite->addPlayer($this->playerOne, true);

        $this->assertTrue($this->gameInvite->isPlayerAdded($this->playerOne));
    }

    public function testIsPlayerAdded(): void
    {
        $this->fullConfig();
        $this->gameInvite->addPlayer($this->playerOne, true);

        $this->assertTrue($this->gameInvite->isPlayerAdded($this->playerOne));
        $this->assertFalse($this->gameInvite->isPlayerAdded($this->playerTwo));
    }

    public function testThrowExceptionWhenGettingHostAndNotSet(): void
    {
        $this->expectException(GameInviteException::class);
        $this->gameInvite->getHost();
    }

    public function testPlayersAddedAndReturned(): void
    {
        $this->fullConfig();
        $this->gameInvite->addPlayer($this->playerOne, true);
        $this->gameInvite->addPlayer($this->playerTwo);

        $expectedPlayerIds = [$this->playerOne->getId(), $this->playerTwo->getId()];
        $actualPlayerIds = array_map(fn($player) => $player->getId(), $this->gameInvite->getPlayers());
        sort($expectedPlayerIds);
        sort($actualPlayerIds);

        $this->assertEquals($expectedPlayerIds, $actualPlayerIds);
    }

    public function testIsHostThrowsExceptionIfNoHost(): void
    {
        $this->expectException(GameInviteException::class);
        $this->gameInvite->isHost($this->playerOne);
    }

    public function testIsHostWhenHostAdded(): void
    {
        $this->fullConfig();
        $this->gameInvite->addPlayer($this->playerOne, true);
        $this->gameInvite->addPlayer($this->playerTwo);

        $this->assertTrue($this->gameInvite->isHost($this->playerOne));
        $this->assertFalse($this->gameInvite->isHost($this->playerTwo));
    }

    public function testThrowExceptionWhenToArrayWithoutUpfrontSetup(): void
    {
        $this->expectException(GameInviteException::class);
        $this->gameInvite->toArray();
    }

    public function testToArray(): void
    {
        $this->fullConfig();
        $this->gameInvite->addPlayer($this->playerOne, true);
        $this->gameInvite->addPlayer($this->playerTwo);

        $expected = [
            'id' => $this->gameInvite->getId(),
            'host' => ['name' => $this->gameInvite->getHost()->getName()],
            'options' => array_map(fn($option) => $option->getConfiguredValue(), $this->gameInvite->getGameSetup()->getAllOptions()),
            'players' => array_map(fn($player) => ['name' => $player->getName()], $this->gameInvite->getPlayers()),
        ];

        $this->assertEquals($expected, $this->gameInvite->toArray());
    }
}
