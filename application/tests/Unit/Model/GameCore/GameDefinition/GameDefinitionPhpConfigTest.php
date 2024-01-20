<?php

namespace Tests\Unit\Model\GameCore\GameDefinition;

use App\Models\GameCore\GameDefinition\GameDefinition;
use App\Models\GameCore\GameDefinition\GameDefinitionException;
use App\Models\GameCore\GameDefinition\GameDefinitionPhpConfig;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\TestCase;

class GameDefinitionPhpConfigTest extends TestCase
{
    protected string $slug = 'test-slug';
    protected array $definition = [
        'name' => 'Tic Tac Toe',
        'description' => 'Famous Tic Tac Toe game that you can now play with friends online!',
        'numberOfPlayers' => [2],
        'durationInMinutes' => 1,
        'minPlayerAge' => 4,
        'isActive' => true,
    ];

    protected function mockConfigFacade(?array $definition = []): void
    {
        if ($definition === []) {
            $definition = $this->definition;
        }

        Config::shouldReceive('get')
            ->once()
            ->with('games.gameDefinition.' . $this->slug )
            ->andReturn($definition);
    }

    public function testGameDefinitionCreated(): void
    {
        $this->mockConfigFacade();
        $gameConfig = new GameDefinitionPhpConfig($this->slug);
        $this->assertInstanceOf(GameDefinition::class, $gameConfig);
    }

    public function testThrowExceptionIfNoSlugInConfig(): void
    {
        $this->expectException(GameDefinitionException::class);
        $this->mockConfigFacade(null);
        new GameDefinitionPhpConfig($this->slug);
    }

    public function testThrowExceptionIfNoNameInConfig(): void
    {
        $this->expectException(GameDefinitionException::class);

        $definition = $this->definition;
        unset($definition['name']);

        $this->mockConfigFacade($definition);

        new GameDefinitionPhpConfig($this->slug);
    }

    public function testThrowExceptionIfNoNumberOfPlayersInConfig(): void
    {
        $this->expectException(GameDefinitionException::class);

        $definition = $this->definition;
        unset($definition['numberOfPlayers']);

        $this->mockConfigFacade($definition);

        new GameDefinitionPhpConfig($this->slug);
    }

    public function testThrowExceptionIfNoIsActiveInConfig(): void
    {
        $this->expectException(GameDefinitionException::class);

        $definition = $this->definition;
        unset($definition['isActive']);

        $this->mockConfigFacade($definition);

        new GameDefinitionPhpConfig($this->slug);
    }

    public function testGetName(): void
    {
        $this->mockConfigFacade();
        $gameConfig = new GameDefinitionPhpConfig($this->slug);
        $this->assertEquals($this->definition['name'], $gameConfig->getName());
    }

    public function testGetSlug(): void
    {
        $this->mockConfigFacade();
        $gameConfig = new GameDefinitionPhpConfig($this->slug);
        $this->assertEquals($this->slug, $gameConfig->getSlug());
    }

    public function testGetDescription(): void
    {
        $this->mockConfigFacade();
        $gameConfig = new GameDefinitionPhpConfig($this->slug);
        $this->assertEquals($this->definition['description'], $gameConfig->getDescription());
    }

    public function testGetNumberOfPlayers(): void
    {
        $this->mockConfigFacade();
        $gameConfig = new GameDefinitionPhpConfig($this->slug);
        $this->assertEquals($this->definition['numberOfPlayers'], $gameConfig->getNumberOfPlayers());
    }

    public function testGetNumberOfPlayersDecriptionWithOneNumber(): void
    {
        $definition = array_replace($this->definition, ['numberOfPlayers' => [2]]);
        $this->mockConfigFacade($definition);
        $gameConfig = new GameDefinitionPhpConfig($this->slug);
        $this->assertEquals('2', $gameConfig->getNumberOfPlayersDescription());
    }

    public function testGetNumberOfPlayersDecriptionWithConsecutiveNumbers(): void
    {
        $definition = array_replace($this->definition, ['numberOfPlayers' => [2, 3, 4]]);
        $this->mockConfigFacade($definition);
        $gameConfig = new GameDefinitionPhpConfig($this->slug);
        $this->assertEquals('2-4', $gameConfig->getNumberOfPlayersDescription());
    }

    public function testGetNumberOfPlayersDecriptionWithNonConsecutiveNumbers(): void
    {
        $definition = array_replace($this->definition, ['numberOfPlayers' => [2, 4, 6]]);
        $this->mockConfigFacade($definition);
        $gameConfig = new GameDefinitionPhpConfig($this->slug);
        $this->assertEquals('2, 4, 6', $gameConfig->getNumberOfPlayersDescription());
    }

    public function testGetDurationInMinutes(): void
    {
        $this->mockConfigFacade();
        $gameConfig = new GameDefinitionPhpConfig($this->slug);
        $this->assertEquals($this->definition['durationInMinutes'], $gameConfig->getDurationInMinutes());
    }

    public function testGetMinPlayerAge(): void
    {
        $this->mockConfigFacade();
        $gameConfig = new GameDefinitionPhpConfig($this->slug);
        $this->assertEquals($this->definition['minPlayerAge'], $gameConfig->getMinPlayerAge());
    }

    public function testGetIsActive(): void
    {
        $this->mockConfigFacade();
        $gameConfig = new GameDefinitionPhpConfig($this->slug);
        $this->assertEquals($this->definition['isActive'], $gameConfig->isActive());
    }
}
