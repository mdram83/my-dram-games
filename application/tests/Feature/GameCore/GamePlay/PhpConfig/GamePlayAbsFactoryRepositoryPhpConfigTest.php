<?php

namespace Tests\Feature\GameCore\GamePlay\PhpConfig;

use App\GameCore\GameBox\GameBoxException;
use App\GameCore\GamePlay\GamePlayAbsFactory;
use App\GameCore\GamePlay\GamePlayAbsFactoryRepository;
use App\GameCore\GamePlay\GamePlayException;
use App\GameCore\GamePlay\PhpConfig\GamePlayAbsFactoryRepositoryPhpConfig;
use App\GameCore\GameSetup\GameSetupAbsFactory;
use App\GameCore\GameSetup\GameSetupException;
use App\GameCore\GameSetup\PhpConfig\GameSetupAbsFactoryRepositoryPhpConfig;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class GamePlayAbsFactoryRepositoryPhpConfigTest extends TestCase
{
    protected GamePlayAbsFactoryRepositoryPhpConfig $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = App::make(GamePlayAbsFactoryRepository::class);
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(GamePlayAbsFactoryRepository::class, $this->repository);
    }

    public function testThrowExceptionWhenNotExistingSlug(): void
    {
        $this->expectException(GameBoxException::class);
        $this->repository->getOne('slug-that-does-not-exist');
    }

    public function testThrowExceptionWhenFactoryClassIsNotExisting(): void
    {
        $this->expectException(GamePlayException::class);
        $this->expectExceptionMessage(GamePlayException::MESSAGE_NO_ABS_FACTORY);

        $slug = 'testing-slug';

        Config::shouldReceive('get')
            ->once()
            ->with('games.box.' . $slug)
            ->andReturn(true);

        Config::shouldReceive('get')
            ->once()
            ->with('games.box.' . $slug . '.' . GamePlayAbsFactoryRepositoryPhpConfig::GAME_PLAY_ABS_FACTORY_KEY )
            ->andReturn('NotExistingClassName');
        $this->repository->getOne($slug);
    }

    public function testThrowExceptionWhenFactoryClassIsNotProperInterface(): void
    {
        $this->expectException(GamePlayException::class);
        $this->expectExceptionMessage(GamePlayException::MESSAGE_NO_ABS_FACTORY);

        $slug = 'testing-slug';

        Config::shouldReceive('get')
            ->once()
            ->with('games.box.' . $slug)
            ->andReturn(true);

        Config::shouldReceive('get')
            ->once()
            ->with('games.box.' . $slug . '.' . GamePlayAbsFactoryRepositoryPhpConfig::GAME_PLAY_ABS_FACTORY_KEY )
            ->andReturn('\Illuminate\Support\Facades\App');

        $this->repository->getOne($slug);
    }

    public function testGetOneWithProperSlugAndConfiguration(): void
    {
        $slug = 'tic-tac-toe';
        $this->assertInstanceOf(GamePlayAbsFactory::class, $this->repository->getOne($slug));
    }
}
