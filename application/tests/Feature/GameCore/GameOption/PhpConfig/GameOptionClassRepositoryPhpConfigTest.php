<?php

namespace Tests\Feature\GameCore\GameOption\PhpConfig;

use App\GameCore\GameOption\GameOptionException;
use App\GameCore\GameOption\GameOptionClassRepository;
use App\GameCore\GameOption\PhpConfig\GameOptionClassClassRepositoryPhpConfig;
use Tests\TestCase;

class GameOptionClassRepositoryPhpConfigTest extends TestCase
{
    public function testInstanceOfGameOptionRepository(): void
    {
        $this->assertInstanceOf(GameOptionClassRepository::class, new GameOptionClassClassRepositoryPhpConfig());
    }

    public function testGetOneThrowExceptionForNotExistingGameOption(): void
    {
        $this->expectException(GameOptionException::class);
        $this->expectExceptionMessage(GameOptionException::MESSAGE_OPTION_NOT_EXIST);

        $repository = new GameOptionClassClassRepositoryPhpConfig();
        $repository->getOne('definitely-missing-game-123-option');
    }

    public function testGetOneReturnGameOptionClass(): void
    {
        $repository = new GameOptionClassClassRepositoryPhpConfig();
        $class = $repository->getOne('autostart');

        $this->assertNotNull($class);
        $this->assertIsString($class);
    }
}
