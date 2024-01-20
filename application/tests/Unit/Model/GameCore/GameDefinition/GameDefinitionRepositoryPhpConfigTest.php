<?php

namespace Tests\Unit\Model\GameCore\GameDefinition;

use App\Models\GameCore\GameDefinition\GameDefinitionFactoryPhpConfig;
use App\Models\GameCore\GameDefinition\GameDefinitionPhpConfig;
use App\Models\GameCore\GameDefinition\GameDefinitionRepositoryPhpConfig;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\TestCase;

class GameDefinitionRepositoryPhpConfigTest extends TestCase
{
    public function testGetOne(): void
    {
        $mockGameDefinition = $this->createMock(GameDefinitionPhpConfig::class);
        $mockFactory = $this->createMock(GameDefinitionFactoryPhpConfig::class);
        $mockFactory->method('create')->willReturn($mockGameDefinition);

        $repository = new GameDefinitionRepositoryPhpConfig($mockFactory);
        $gameDefinition = $repository->getOne('test-slug');

        $this->assertInstanceOf(GameDefinitionPhpConfig::class, $gameDefinition);
    }

    public function testGetAll(): void
    {
        $mockGameDefinition = $this->createMock(GameDefinitionPhpConfig::class);
        $mockFactory = $this->createMock(GameDefinitionFactoryPhpConfig::class);
        $mockFactory->method('create')->willReturn($mockGameDefinition);

        Config::shouldReceive('get')
                ->once()
                ->with('games.gameDefinition')
                ->andReturn(['test-slug-one' => [], 'test-slug-2' => []]);

        $repository = new GameDefinitionRepositoryPhpConfig($mockFactory);
        $games = $repository->getAll();

        $this->assertInstanceOf(GameDefinitionPhpConfig::class, $games[0]);
        $this->assertInstanceOf(GameDefinitionPhpConfig::class, $games[1]);
    }
}
