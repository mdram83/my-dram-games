<?php

namespace Tests\Feature\GameCore\GameSetup;

use App\GameCore\GameBox\GameBoxException;
use App\GameCore\GameSetup\GameSetupAbsFactory;
use App\GameCore\GameSetup\GameSetupAbsFactoryRepository;
use App\GameCore\GameSetup\GameSetupException;
use App\GameCore\GameSetup\PhpConfig\GameSetupAbsFactoryRepositoryPhpConfig;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class GameSetupAbsFactoryRepositoryPhpConfigTest extends TestCase
{
    protected GameSetupAbsFactoryRepositoryPhpConfig $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = App::make(GameSetupAbsFactoryRepository::class);
    }

    public function testInstanceMatchingInterface(): void
    {
        $this->assertInstanceOf(GameSetupAbsFactoryRepository::class, $this->repository);
    }

    public function testThrowExceptionWhenNotExistingSlug(): void
    {
        $this->expectException(GameBoxException::class);
        $this->repository->getOne('slug-that-does-not-exist');
    }

    public function testThrowExceptionWhenFactoryClassIsNotExisting(): void
    {
        $this->expectException(GameSetupException::class);
        $this->expectExceptionMessage(GameSetupException::MESSAGE_NO_ABS_FACTORY);

        $slug = 'testing-slug';

        Config::shouldReceive('get')
            ->once()
            ->with('games.box.' . $slug)
            ->andReturn(true);

        Config::shouldReceive('get')
            ->once()
            ->with('games.box.' . $slug . '.' . GameSetupAbsFactoryRepositoryPhpConfig::GAME_SETUP_ABS_FACTORY_KEY )
            ->andReturn('NotExistingClassName');
        $this->repository->getOne($slug);
    }

    public function testThrowExceptionWhenFactoryClassIsNotProperInterface(): void
    {
        $this->expectException(GameSetupException::class);
        $this->expectExceptionMessage(GameSetupException::MESSAGE_NO_ABS_FACTORY);

        $slug = 'testing-slug';

        Config::shouldReceive('get')
            ->once()
            ->with('games.box.' . $slug)
            ->andReturn(true);

        Config::shouldReceive('get')
            ->once()
            ->with('games.box.' . $slug . '.' . GameSetupAbsFactoryRepositoryPhpConfig::GAME_SETUP_ABS_FACTORY_KEY )
            ->andReturn('\Illuminate\Support\Facades\App');

        $this->repository->getOne($slug);
    }

    public function testGetOneWithProperSlugAndConfiguration(): void
    {
        $slug = 'tic-tac-toe';
        $this->assertInstanceOf(GameSetupAbsFactory::class, $this->repository->getOne($slug));
    }
}
