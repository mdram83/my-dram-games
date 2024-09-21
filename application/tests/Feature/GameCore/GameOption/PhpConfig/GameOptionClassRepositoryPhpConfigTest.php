<?php

namespace Tests\Feature\GameCore\GameOption\PhpConfig;

use App\GameCore\GameOption\GameOptionClassRepository;
use App\GameCore\GameOption\PhpConfig\GameOptionClassRepositoryPhpConfig;
use MyDramGames\Core\Exceptions\GameOptionException;
use Tests\TestCase;

class GameOptionClassRepositoryPhpConfigTest extends TestCase
{
    protected GameOptionClassRepositoryPhpConfig $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new GameOptionClassRepositoryPhpConfig();
    }
    public function testInstanceOfGameOptionRepository(): void
    {
        $this->assertInstanceOf(GameOptionClassRepository::class, $this->repository);
    }

    public function testGetOptionClassnameThrowExceptionForNotExistingGameOption(): void
    {
        $this->expectException(GameOptionException::class);
        $this->expectExceptionMessage(GameOptionException::MESSAGE_INCOMPATIBLE_VALUE);

        $this->repository->getOptionClassname('definitely-missing-game-123-option');
    }

    public function testGetOptionClassName(): void
    {
        $class = $this->repository->getOptionClassname('autostart');

        $this->assertNotNull($class);
        $this->assertIsString($class);
    }

    public function testGetValueClassnameThrowExceptionForNotExistingGameOption(): void
    {
        $this->expectException(GameOptionException::class);
        $this->expectExceptionMessage(GameOptionException::MESSAGE_INCOMPATIBLE_VALUE);

        $this->repository->getValueClassname('definitely-missing-game-123-option');
    }

    public function testGetValueClassname(): void
    {
        $class = $this->repository->getValueClassname('autostart');

        $this->assertNotNull($class);
        $this->assertIsString($class);
    }
}
