<?php

namespace Tests\Unit\Model\GameCore\GameDefinition;

use App\Models\GameCore\GameDefinition\GameDefinitionFactoryPhpConfig;
use App\Models\GameCore\GameDefinition\GameDefinitionPhpConfig;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\TestCase;

class GameDefinitionFactoryPhpConfigTest extends TestCase
{
    public function test_createGameDefinition(): void
    {
        $slug = 'test-slug';
        $definition = [
            'name' => 'Tic Tac Toe',
            'description' => 'Famous Tic Tac Toe game that you can now play with friends online!',
            'numberOfPlayers' => [2],
            'durationInMinutes' => 1,
            'minPlayerAge' => 4,
            'isActive' => true,
        ];

        Config::shouldReceive('get')
            ->once()
            ->with('games.gameDefinition.' . $slug )
            ->andReturn($definition);

        $factory = new GameDefinitionFactoryPhpConfig();
        $gameDefinition = $factory->create($slug);
        $this->assertInstanceOf(GameDefinitionPhpConfig::class, $gameDefinition);
    }
}
