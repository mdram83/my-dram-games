<?php

namespace Tests\Feature\GameCore\GameInvite\Eloquent;

use App\GameCore\GameInvite\Eloquent\GameInviteEloquent;
use App\GameCore\GameInvite\GameInviteException;
use App\GameCore\GameBox\GameBox;
use App\GameCore\GameBox\GameBoxRepository;
use App\GameCore\GameSetup\GameSetup;
use App\GameCore\Player\Player;
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
    protected array $options = ['numberOfPlayers' => 2, 'autostart' => false];

    public function setUp(): void
    {
        parent::setUp();

        $this->playerOne = User::factory()->create();
        $this->gameBox = $this->createMock(GameBox::class);
        $this->gameInvite = new GameInviteEloquent(App::make(GameBoxRepository::class));
    }

    protected function configureMocks(array $numberOfPlayers): void
    {
        $gameSetupMock = $this->createMock(GameSetup::class);
        $gameSetupMock->method('getNumberOfPlayers')->willReturn($numberOfPlayers);

        $this->gameBox = $this->createMock(GameBox::class);
        $this->gameBox->method('getGameSetup')->willReturn($gameSetupMock);
    }

    protected function configureGameForXPlayers(array $allowedNumberOfPlayers = [2], int $numberOfPlayers = 2): void
    {
        $this->configureMocks($allowedNumberOfPlayers);
        $this->gameInvite->setGameBox($this->gameBox);
    }

    protected function configurePlayerTwo(): void
    {
        $this->playerTwo = User::factory()->create();
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

    public function testThrowExceptionWhenOverwritingGameDefinition(): void
    {
        $this->expectException(GameInviteException::class);
        $this->gameInvite->setGameBox($this->gameBox);
        $this->gameInvite->setGameBox($this->gameBox);
    }

    public function testGameDefinitionSetAndReturned(): void
    {
        $this->gameInvite->setGameBox($this->gameBox);
        $this->assertSame($this->gameBox, $this->gameInvite->getGameBox());
    }

    public function testThrowExceptionWhenGettingUnsetGameDefinition(): void
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

        $this->configureMocks([$this->options['numberOfPlayers']]);
        $this->configureGameForXPlayers([2, 3]);
        $this->gameInvite->setOptions($this->options);
        $this->gameInvite->setOptions($this->options);
    }

    public function testSetOptionsAndGetGameSetup(): void
    {
        $this->configureMocks([$this->options['numberOfPlayers']]);
        $this->configureGameForXPlayers([2, 3]);
        $this->gameInvite->setOptions($this->options);
        $this->assertInstanceOf(GameSetup::class, $this->gameInvite->getGameSetup());
    }

    // Setting/Getting Players

    public function testThrowExceptionWhenAddingPlayerWithoutSettingOptions(): void
    {
        $this->expectException(GameInviteException::class);
        $this->gameInvite->addPlayer($this->playerOne);
    }

    public function testThrowExceptionWhenAddingTooManyPlayers(): void
    {
        $this->expectException(GameInviteException::class);

        $this->configureGameForXPlayers();
        $this->configurePlayerTwo();
        $this->gameInvite->setOptions($this->options);

        $playerThree = $this->createMock(Player::class);
        $playerThree->method('getId')->willReturn(3);

        $this->gameInvite->addPlayer($this->playerOne, true);
        $this->gameInvite->addPlayer($this->playerTwo);
        $this->gameInvite->addPlayer($playerThree);
    }

    public function testThrowExceptionWhenAddingSamePlayerManyTimes(): void
    {
        $this->expectException(GameInviteException::class);

        $this->configureGameForXPlayers();
        $this->gameInvite->setOptions($this->options);
        $this->gameInvite->addPlayer($this->playerOne);
        $this->gameInvite->addPlayer($this->playerOne);
    }

    public function testThrowExceptionWhenAddingHostMoreThenOnce(): void
    {
        $this->expectException(GameInviteException::class);

        $this->configureGameForXPlayers();
        $this->configurePlayerTwo();

        $this->gameInvite->setOptions($this->options);
        $this->gameInvite->addPlayer($this->playerOne, true);
        $this->gameInvite->addPlayer($this->playerTwo, true);
    }

    public function testThrowExceptionWhenAddingPlayerToGameWithoutHost(): void
    {
        $this->expectException(GameInviteException::class);

        $this->configureGameForXPlayers();
        $this->configurePlayerTwo();

        $this->gameInvite->setOptions($this->options);
        $this->gameInvite->addPlayer($this->playerOne);
        $this->gameInvite->addPlayer($this->playerTwo);
    }

    public function testGameHostAddedAndReturned(): void
    {
        $this->configureGameForXPlayers();
        $this->gameInvite->setOptions($this->options);
        $this->gameInvite->addPlayer($this->playerOne, true);

        $this->assertEquals($this->playerOne->getId(), $this->gameInvite->getHost()->getId());
    }

    public function testGameHostAddedAndBecomesAlsoOneOfPlayers(): void
    {
        $this->configureGameForXPlayers();
        $this->gameInvite->setOptions($this->options);
        $this->gameInvite->addPlayer($this->playerOne, true);

        $hostId = $this->playerOne->getId();
        $playerIds = array_map(fn($player) => $player->getId(), $this->gameInvite->getPlayers());

        $this->assertTrue(in_array($hostId, $playerIds));
    }

    public function testIsPlayerAdded(): void
    {
        $this->configureGameForXPlayers();
        $this->configurePlayerTwo();
        $this->gameInvite->setOptions($this->options);
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
        $this->configureGameForXPlayers();
        $this->configurePlayerTwo();

        $this->gameInvite->setOptions($this->options);
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
        $this->configureGameForXPlayers();
        $this->configurePlayerTwo();

        $this->gameInvite->setOptions($this->options);
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

    // TODO adjust to remove numberOfPlayers completely and replace with gameSetup object with options array
    public function testToArray(): void
    {
        $this->configureGameForXPlayers();
        $this->configurePlayerTwo();

        $this->gameInvite->setOptions($this->options);
        $this->gameInvite->addPlayer($this->playerOne, true);
        $this->gameInvite->addPlayer($this->playerTwo);

        $expectedGameSetup = [];
        foreach ($this->gameInvite->getGameSetup()->getAllOptions() as $name => $value) {
            $expectedGameSetup[$name] = $value[0];
        }

        $expected = [
            'id' => $this->gameInvite->getId(),
            'host' => ['name' => $this->gameInvite->getHost()->getName()],
            'gameSetup' => $expectedGameSetup,
            'players' => array_map(fn($player) => ['name' => $player->getName()], $this->gameInvite->getPlayers()),
        ];

        $this->assertEquals($expected, $this->gameInvite->toArray());
    }
}
