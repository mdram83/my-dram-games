<?php

namespace Tests\Feature\Extensions\Core\GameInvite;

use App\Extensions\Core\GameInvite\GameInviteEloquent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use MyDramGames\Core\Exceptions\GameInviteException;
use MyDramGames\Core\GameBox\GameBox;
use MyDramGames\Core\GameBox\GameBoxRepository;
use MyDramGames\Core\GameOption\GameOptionConfigurationCollection;
use MyDramGames\Core\GameOption\GameOptionConfigurationCollectionPowered;
use MyDramGames\Core\GameOption\GameOptionConfigurationGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueAutostartGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueForfeitAfterGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueNumberOfPlayersGeneric;
use MyDramGames\Core\GameSetup\GameSetup;
use MyDramGames\Utils\Php\Collection\CollectionEngine;
use MyDramGames\Utils\Player\Player;
use MyDramGames\Utils\Player\PlayerCollection;
use Tests\TestCase;

class GameInviteEloquentTest extends TestCase
{
    use RefreshDatabase;

    protected GameInviteEloquent $gameInvite;
    protected Player $playerOne;
    protected Player $playerTwo;
    protected GameBox $gameBox;
    protected GameOptionConfigurationCollection $options;

    public function setUp(): void
    {
        parent::setUp();

        $this->playerOne = User::factory()->create();
        $this->playerTwo = User::factory()->create();

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

        $this->gameBox = App::make(GameBoxRepository::class)->getOne('tic-tac-toe');
        $this->gameInvite = new GameInviteEloquent(
            App::make(GameBoxRepository::class),
            App::make(PlayerCollection::class),
            App::make(GameOptionConfigurationCollection::class),
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

    public function testThrowExceptionWhenGettingGameSetupWithoutSettingGameBox(): void
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
            App::make(PlayerCollection::class),
            App::make(GameOptionConfigurationCollection::class),
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

        $this->assertTrue($this->gameInvite->isPlayer($this->playerOne));
    }

    public function testIsPlayerAdded(): void
    {
        $this->fullConfig();
        $this->gameInvite->addPlayer($this->playerOne, true);

        $this->assertTrue($this->gameInvite->isPlayer($this->playerOne));
        $this->assertFalse($this->gameInvite->isPlayer($this->playerTwo));
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
        $actualPlayerIds = $this->gameInvite->getPlayers()->keys();
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
            'options' => array_map(fn($option) => $option->getConfiguredValue(), $this->gameInvite->getGameSetup()->getAllOptions()->toArray()),
            'players' => array_values(array_map(fn($player) => ['name' => $player->getName()], $this->gameInvite->getPlayers()->toArray())),
        ];

        var_dump($this->gameInvite->toArray());

        $this->assertEquals($expected, $this->gameInvite->toArray());
    }
}
