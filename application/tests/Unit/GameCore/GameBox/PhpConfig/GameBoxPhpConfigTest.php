<?php

namespace Tests\Unit\GameCore\GameBox\PhpConfig;

use App\GameCore\GameBox\GameBox;
use App\GameCore\GameBox\GameBoxException;
use App\GameCore\GameBox\PhpConfig\GameBoxPhpConfig;
use App\GameCore\GameSetup\GameSetup;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\TestCase;

class GameBoxPhpConfigTest extends TestCase
{
    protected string $slug = 'test-slug';
    protected array $box = [
        'name' => 'Tic Tac Toe',
        'description' => 'Famous Tic Tac Toe game that you can now play with friends online!',
        'numberOfPlayers' => [2],
        'durationInMinutes' => 1,
        'minPlayerAge' => 4,
        'isActive' => true,
    ];

    protected GameSetup $gameSetupMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->gameSetupMock = $this->createMock(GameSetup::class);
    }

    protected function mockConfigFacade(?array $box = []): void
    {
        if ($box === []) {
            $box = $this->box;
        }

        Config::shouldReceive('get')
            ->once()
            ->with('games.box.' . $this->slug )
            ->andReturn($box);
    }

    public function testGameDefinitionCreated(): void
    {
        $this->mockConfigFacade();
        $gameBox = new GameBoxPhpConfig($this->slug, $this->gameSetupMock);
        $this->assertInstanceOf(GameBox::class, $gameBox);
    }

    public function testThrowExceptionIfNoSlugInConfig(): void
    {
        $this->expectException(GameBoxException::class);
        $this->mockConfigFacade(null);
        new GameBoxPhpConfig($this->slug, $this->gameSetupMock);
    }

    public function testThrowExceptionIfNoNameInConfig(): void
    {
        $this->expectException(GameBoxException::class);

        $box = $this->box;
        unset($box['name']);

        $this->mockConfigFacade($box);

        new GameBoxPhpConfig($this->slug, $this->gameSetupMock);
    }

    public function testThrowExceptionIfNoNumberOfPlayersInConfig(): void
    {
        $this->expectException(GameBoxException::class);

        $box = $this->box;
        unset($box['numberOfPlayers']);

        $this->mockConfigFacade($box);

        new GameBoxPhpConfig($this->slug, $this->gameSetupMock);
    }

    public function testThrowExceptionIfNoIsActiveInConfig(): void
    {
        $this->expectException(GameBoxException::class);

        $box = $this->box;
        unset($box['isActive']);

        $this->mockConfigFacade($box);

        new GameBoxPhpConfig($this->slug, $this->gameSetupMock);
    }

    public function testGetName(): void
    {
        $this->mockConfigFacade();
        $gameBox = new GameBoxPhpConfig($this->slug, $this->gameSetupMock);
        $this->assertEquals($this->box['name'], $gameBox->getName());
    }

    public function testGetSlug(): void
    {
        $this->mockConfigFacade();
        $gameBox = new GameBoxPhpConfig($this->slug, $this->gameSetupMock);
        $this->assertEquals($this->slug, $gameBox->getSlug());
    }

    public function testGetDescription(): void
    {
        $this->mockConfigFacade();
        $gameBox = new GameBoxPhpConfig($this->slug, $this->gameSetupMock);
        $this->assertEquals($this->box['description'], $gameBox->getDescription());
    }

    public function testGetNumberOfPlayers(): void
    {
        $this->mockConfigFacade();
        $gameBox = new GameBoxPhpConfig($this->slug, $this->gameSetupMock);
        $this->assertEquals($this->box['numberOfPlayers'], $gameBox->getNumberOfPlayers());
    }

    public function testGetNumberOfPlayersDecriptionWithOneNumber(): void
    {
        $box = array_replace($this->box, ['numberOfPlayers' => [2]]);
        $this->mockConfigFacade($box);
        $gameBox = new GameBoxPhpConfig($this->slug, $this->gameSetupMock);
        $this->assertEquals('2', $gameBox->getNumberOfPlayersDescription());
    }

    public function testGetNumberOfPlayersDecriptionWithConsecutiveNumbers(): void
    {
        $box = array_replace($this->box, ['numberOfPlayers' => [2, 3, 4]]);
        $this->mockConfigFacade($box);
        $gameBox = new GameBoxPhpConfig($this->slug, $this->gameSetupMock);
        $this->assertEquals('2-4', $gameBox->getNumberOfPlayersDescription());
    }

    public function testGetNumberOfPlayersDecriptionWithNonConsecutiveNumbers(): void
    {
        $box = array_replace($this->box, ['numberOfPlayers' => [2, 4, 6]]);
        $this->mockConfigFacade($box);
        $gameBox = new GameBoxPhpConfig($this->slug, $this->gameSetupMock);
        $this->assertEquals('2, 4, 6', $gameBox->getNumberOfPlayersDescription());
    }

    public function testGetDurationInMinutes(): void
    {
        $this->mockConfigFacade();
        $gameBox = new GameBoxPhpConfig($this->slug, $this->gameSetupMock);
        $this->assertEquals($this->box['durationInMinutes'], $gameBox->getDurationInMinutes());
    }

    public function testGetMinPlayerAge(): void
    {
        $this->mockConfigFacade();
        $gameBox = new GameBoxPhpConfig($this->slug, $this->gameSetupMock);
        $this->assertEquals($this->box['minPlayerAge'], $gameBox->getMinPlayerAge());
    }

    public function testGetIsActive(): void
    {
        $this->mockConfigFacade();
        $gameBox = new GameBoxPhpConfig($this->slug, $this->gameSetupMock);
        $this->assertEquals($this->box['isActive'], $gameBox->isActive());
    }

    public function testToArray(): void
    {
        $this->mockConfigFacade();
        $gameBox = new GameBoxPhpConfig($this->slug, $this->gameSetupMock);
        $this->assertEquals(
            array_merge(['slug' => $this->slug], $this->box, ['numberOfPlayersDescription' => '2']),
            $gameBox->toArray());
    }
}
