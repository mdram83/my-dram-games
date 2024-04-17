<?php

namespace Tests\Feature\GameCore\GamePlay\PhpConfig;

use App\GameCore\GameBox\GameBoxException;
use App\GameCore\GamePlay\GamePlayAbsRepositoryRepository;
use App\GameCore\GamePlay\GamePlayException;
use App\GameCore\GamePlay\PhpConfig\GamePlayAbsRepositoryRepositoryPhpConfig;
use App\Games\TicTacToe\GamePlayTicTacToe;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class GamePlayAbsRepositoryRepositoryPhpConfigTest extends TestCase
{
    protected GamePlayAbsRepositoryRepositoryPhpConfig $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = App::make(GamePlayAbsRepositoryRepository::class);
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(GamePlayAbsRepositoryRepository::class, $this->repository);
    }

    public function testThrowExceptionWhenNotExistingSlug(): void
    {
        $this->expectException(GameBoxException::class);
        $this->repository->getOne('slug-that-does-not-exist');
    }

    public function testThrowExceptionWhenFactoryClassIsNotExisting(): void
    {
        $this->expectException(GamePlayException::class);
        $this->expectExceptionMessage(GamePlayException::MESSAGE_NO_ABS_CLASS);

        $slug = 'testing-slug';

        Config::shouldReceive('get')
            ->once()
            ->with('games.box.' . $slug)
            ->andReturn(true);

        Config::shouldReceive('get')
            ->once()
            ->with('games.box.' . $slug . '.' . GamePlayAbsRepositoryRepositoryPhpConfig::GAME_PLAY_ABS_CLASS_KEY)
            ->andReturn('NotExistingClassName');
        $this->repository->getOne($slug);
    }

    public function testThrowExceptionWhenFactoryClassIsNotProperInterface(): void
    {
        $this->expectException(GamePlayException::class);
        $this->expectExceptionMessage(GamePlayException::MESSAGE_NO_ABS_CLASS);

        $slug = 'testing-slug';

        Config::shouldReceive('get')
            ->once()
            ->with('games.box.' . $slug)
            ->andReturn(true);

        Config::shouldReceive('get')
            ->once()
            ->with('games.box.' . $slug . '.' . GamePlayAbsRepositoryRepositoryPhpConfig::GAME_PLAY_ABS_CLASS_KEY)
            ->andReturn('\Illuminate\Support\Facades\App');

        $this->repository->getOne($slug);
    }

    public function testGetOneWithProperSlugAndConfiguration(): void
    {
        $slug = 'tic-tac-toe';
        $this->assertEquals(GamePlayTicTacToe::class, $this->repository->getOne($slug));
    }
}
