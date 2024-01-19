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

    public function test_game_definition_object_created(): void
    {
        $this->mockConfigFacade();
        $gameConfig = new GameDefinitionPhpConfig($this->slug);
        $this->assertInstanceOf(GameDefinition::class, $gameConfig);
    }

    public function test_throw_exception_if_configuration_has_no_slug(): void
    {
        $this->expectException(GameDefinitionException::class);
        $this->mockConfigFacade(null);
        new GameDefinitionPhpConfig($this->slug);
    }

    public function test_throw_exception_if_configuration_has_no_name(): void
    {
        $this->expectException(GameDefinitionException::class);

        $definition = $this->definition;
        unset($definition['name']);

        $this->mockConfigFacade($definition);

        new GameDefinitionPhpConfig($this->slug);
    }

    public function test_throw_exception_if_configuration_has_no_numberOfPlayers(): void
    {
        $this->expectException(GameDefinitionException::class);

        $definition = $this->definition;
        unset($definition['numberOfPlayers']);

        $this->mockConfigFacade($definition);

        new GameDefinitionPhpConfig($this->slug);
    }

    public function test_throw_exception_if_configuration_has_no_isActive(): void
    {
        $this->expectException(GameDefinitionException::class);

        $definition = $this->definition;
        unset($definition['isActive']);

        $this->mockConfigFacade($definition);

        new GameDefinitionPhpConfig($this->slug);
    }

    public function test_get_name(): void
    {
        $this->mockConfigFacade();
        $gameConfig = new GameDefinitionPhpConfig($this->slug);
        $this->assertEquals($this->definition['name'], $gameConfig->getName());
    }

    public function test_get_slug(): void
    {
        $this->mockConfigFacade();
        $gameConfig = new GameDefinitionPhpConfig($this->slug);
        $this->assertEquals($this->slug, $gameConfig->getSlug());
    }

    public function test_get_description(): void
    {
        $this->mockConfigFacade();
        $gameConfig = new GameDefinitionPhpConfig($this->slug);
        $this->assertEquals($this->definition['description'], $gameConfig->getDescription());
    }

    public function test_get_numberofPlayers(): void
    {
        $this->mockConfigFacade();
        $gameConfig = new GameDefinitionPhpConfig($this->slug);
        $this->assertEquals($this->definition['numberOfPlayers'], $gameConfig->getNumberOfPlayers());
    }

    public function test_get_numberOfPlayersDescription_with_one_number(): void
    {
        $definition = array_replace($this->definition, ['numberOfPlayers' => [2]]);
        $this->mockConfigFacade($definition);
        $gameConfig = new GameDefinitionPhpConfig($this->slug);
        $this->assertEquals('2', $gameConfig->getNumberOfPlayersDescription());
    }

    public function test_get_numberOfPlayersDescription_with_consecutive_numbers(): void
    {
        $definition = array_replace($this->definition, ['numberOfPlayers' => [2, 3, 4]]);
        $this->mockConfigFacade($definition);
        $gameConfig = new GameDefinitionPhpConfig($this->slug);
        $this->assertEquals('2-4', $gameConfig->getNumberOfPlayersDescription());
    }

    public function test_get_numberOfPlayersDescription_with_non_consecutive_numbers(): void
    {
        $definition = array_replace($this->definition, ['numberOfPlayers' => [2, 4, 6]]);
        $this->mockConfigFacade($definition);
        $gameConfig = new GameDefinitionPhpConfig($this->slug);
        $this->assertEquals('2, 4, 6', $gameConfig->getNumberOfPlayersDescription());
    }

    public function test_get_durationInMinutes(): void
    {
        $this->mockConfigFacade();
        $gameConfig = new GameDefinitionPhpConfig($this->slug);
        $this->assertEquals($this->definition['durationInMinutes'], $gameConfig->getDurationInMinutes());
    }

    public function test_get_minPlayerAge(): void
    {
        $this->mockConfigFacade();
        $gameConfig = new GameDefinitionPhpConfig($this->slug);
        $this->assertEquals($this->definition['minPlayerAge'], $gameConfig->getMinPlayerAge());
    }

    public function test_get_isActive(): void
    {
        $this->mockConfigFacade();
        $gameConfig = new GameDefinitionPhpConfig($this->slug);
        $this->assertEquals($this->definition['isActive'], $gameConfig->isActive());
    }
}
