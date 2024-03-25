<?php

namespace Tests\Feature\GameCore\GameElements\GameMove\PhpConfig;

use App\GameCore\GameBox\GameBoxException;
use App\GameCore\GameElements\GameMove\GameMoveAbsFactory;
use App\GameCore\GameElements\GameMove\GameMoveAbsFactoryRepository;
use App\GameCore\GameElements\GameMove\GameMoveException;
use App\GameCore\GameElements\GameMove\PhpConfig\GameMoveAbsFactoryRepositoryPhpConfig;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class GameMoveAbsFactoryRepositoryPhpConfigTest extends TestCase
{
    protected GameMoveAbsFactoryRepositoryPhpConfig $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = App::make(GameMoveAbsFactoryRepository::class);
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(GameMoveAbsFactoryRepository::class, $this->repository);
    }

    public function testThrowExceptionWhenNotExistingSlug(): void
    {
        $this->expectException(GameBoxException::class);
        $this->repository->getOne('slug-that-does-not-exist');
    }

    public function testThrowExceptionWhenFactoryClassIsNotExisting(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_NO_ABS_FACTORY);

        $slug = 'testing-slug';

        Config::shouldReceive('get')
            ->once()
            ->with('games.box.' . $slug)
            ->andReturn(true);

        Config::shouldReceive('get')
            ->once()
            ->with('games.box.' . $slug . '.' . GameMoveAbsFactoryRepositoryPhpConfig::GAME_MOVE_ABS_FACTORY_KEY )
            ->andReturn('NotExistingClassName');
        $this->repository->getOne($slug);
    }

    public function testThrowExceptionWhenFactoryClassIsNotProperInterface(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_NO_ABS_FACTORY);

        $slug = 'testing-slug';

        Config::shouldReceive('get')
            ->once()
            ->with('games.box.' . $slug)
            ->andReturn(true);

        Config::shouldReceive('get')
            ->once()
            ->with('games.box.' . $slug . '.' . GameMoveAbsFactoryRepositoryPhpConfig::GAME_MOVE_ABS_FACTORY_KEY )
            ->andReturn('\Illuminate\Support\Facades\App');

        $this->repository->getOne($slug);
    }

    public function testGetOneWithProperSlugAndConfiguration(): void
    {
        $slug = 'tic-tac-toe';
        $this->assertInstanceOf(GameMoveAbsFactory::class, $this->repository->getOne($slug));
    }
}
