<?php

namespace Tests\Feature\Model\GameCore\Game;

use App\Models\GameCore\Game\GameEloquent;
use App\Models\GameCore\Game\GameException;
use App\Models\GameCore\GameDefinition\GameDefinition;
use App\Models\GameCore\Player\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameEloquentTest extends TestCase
{
    use RefreshDatabase;

    protected GameEloquent $game;
    protected Player $playerOne;
    protected Player $playerTwo;
    protected GameDefinition $gameDefinition;

    public function setUp(): void
    {
        parent::setUp();

        $this->playerOne = User::factory()->create();
        $this->gameDefinition = $this->createMock(GameDefinition::class);
        $this->game = new GameEloquent();
    }

    protected function configureGameDefinitionMock(array $numberOfPlayers): void
    {
        $this->gameDefinition->method('getNumberOfPlayers')->willReturn($numberOfPlayers);
    }

    protected function configureGameForXPlayers(array $allowedNumberOfPlayers = [2], int $numberOfPlayers = 2): void
    {
        $this->configureGameDefinitionMock($allowedNumberOfPlayers);
        $this->game->setGameDefinition($this->gameDefinition);
        $this->game->setNumberOfPlayers($numberOfPlayers);
    }

    protected function configurePlayerTwo(): void
    {
        $this->playerTwo = $this->createMock(Player::class);
        $this->playerTwo->method('getId')->willReturn(2);
    }

    public function testGameEloquentObjectCreated(): void
    {
        $this->assertInstanceOf(GameEloquent::class, $this->game);
    }

    // Setting/Getting GameDefinition

    public function testThrowExceptionWhenOverwritingGameDefinition(): void
    {
        $this->expectException(GameException::class);
        $this->game->setGameDefinition($this->gameDefinition);
        $this->game->setGameDefinition($this->gameDefinition);
    }

    public function testGameDefinitionSetAndReturned(): void
    {
        $this->game->setGameDefinition($this->gameDefinition);
        $this->assertSame($this->gameDefinition, $this->game->getGameDefinition());
    }

    public function testThrowExceptionWhenGettingUnsetGameDefinition(): void
    {
        $this->expectException(GameException::class);
        $this->game->getGameDefinition();
    }

    // Setting/Getting Number Of Players

    public function testThrowExceptionWhenSettingNumberOfPlayersWithoutSettingGameDefinition(): void
    {
        $this->expectException(GameException::class);
        $this->game->setNumberOfPlayers(2);
    }

    public function testThrowExceptionWhenSettingNumberOfPlayersNotMatchingGameDefinition(): void
    {
        $this->expectException(GameException::class);

        $this->configureGameDefinitionMock([2, 4]);
        $this->game->setGameDefinition($this->gameDefinition);
        $this->game->setNumberOfPlayers(3);
    }

    public function testThrowExceptionWhenGettingUnsetNumberOfPlayers(): void
    {
        $this->expectException(GameException::class);
        $this->game->getNumberOfPlayers();
    }

    public function testThrowExceptionWhenSettingNumberOfPlayersAgain(): void
    {
        $this->expectException(GameException::class);
        $this->configureGameDefinitionMock([2]);
        $this->game->setGameDefinition($this->gameDefinition);
        $this->game->setNumberOfPlayers(2);
        $this->game->setNumberOfPlayers(2);
    }

    public function testSetAndReturnValidNumberOfPlayers(): void
    {
        $allowedNumberOfPlayers = [2, 4];
        $numberOfPlayers = 2;

        $this->configureGameDefinitionMock($allowedNumberOfPlayers);
        $this->game->setGameDefinition($this->gameDefinition);
        $this->game->setNumberOfPlayers($numberOfPlayers);
        $this->assertEquals($numberOfPlayers, $this->game->getNumberOfPlayers());
    }

    // Setting/Getting Players

    public function testThrowExceptionWhenAddingPlayerWithoutSettingNumberOfPlayers(): void
    {
        $this->expectException(GameException::class);
        $this->game->addPlayer($this->playerOne);
    }

    public function testThrowExceptionWhenAddingTooManyPlayers(): void
    {
        $this->expectException(GameException::class);

        $this->configureGameForXPlayers();
        $this->configurePlayerTwo();

        $playerThree = $this->createMock(Player::class);
        $playerThree->method('getId')->willReturn(3);

        $this->game->addPlayer($this->playerOne, true);
        $this->game->addPlayer($this->playerTwo);
        $this->game->addPlayer($playerThree);
    }

    public function testThrowExceptionWhenAddingSamePlayerManyTimes(): void
    {
        $this->expectException(GameException::class);
        $this->configureGameForXPlayers();
        $this->game->addPlayer($this->playerOne);
        $this->game->addPlayer($this->playerOne);
    }

    public function testThrowExceptionWhenAddingHostMoreThenOnce(): void
    {
        $this->expectException(GameException::class);
        $this->configureGameForXPlayers();
        $this->configurePlayerTwo();

        $this->game->addPlayer($this->playerOne, true);
        $this->game->addPlayer($this->playerTwo, true);
    }

    public function testThrowExceptionWhenAddingPlayerToGameWithoutHost(): void
    {
        $this->expectException(GameException::class);
        $this->configureGameForXPlayers();
        $this->configurePlayerTwo();

        $this->game->addPlayer($this->playerOne);
        $this->game->addPlayer($this->playerTwo);
    }

    public function testGameHostAddedAndBecomesAlsoOneOfPlayers(): void
    {
        $this->configureGameForXPlayers();
        $this->game->addPlayer($this->playerOne, true);

        $this->assertTrue(in_array($this->playerOne, $this->game->getPlayers()));
        $this->assertSame($this->playerOne, $this->game->getHost());
    }

    public function testThrowExceptionWhenGettingHostAndNotSet(): void
    {
        $this->expectException(GameException::class);
        $this->game->getHost();
    }

    public function testPlayersAddedAndReturned(): void
    {
        $this->configureGameForXPlayers();
        $this->configurePlayerTwo();

        $this->game->addPlayer($this->playerOne, true);
        $this->game->addPlayer($this->playerTwo);

        $this->assertEquals([$this->playerOne, $this->playerTwo], $this->game->getPlayers());
    }
}
