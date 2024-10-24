<?php

namespace Tests\Feature\Extensions\Core\GameBox;

use App\Extensions\Core\GameBox\GameBoxRepositoryPhpConfig;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use MyDramGames\Core\Exceptions\GameBoxException;
use MyDramGames\Core\GameBox\GameBox;
use MyDramGames\Core\GameBox\GameBoxRepository;
use Tests\TestCase;

class GameBoxRepositoryPhpConfigTest extends TestCase
{
    protected GameBoxRepositoryPhpConfig $repository;
    protected array $config;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = App::make(GameBoxRepository::class);
        $this->config = Config::get('games.box');
    }

    public function testInstanceOfGameDefinitionRepository(): void
    {
        $this->assertInstanceOf(GameBoxRepository::class, $this->repository);
    }

    public function testGetOneExisting(): void
    {
        $slug = array_keys($this->config)[0];
        $gameBox = $this->repository->getOne($slug);
        $this->assertInstanceOf(GameBox::class, $gameBox);
    }

    public function testGetOneMissing(): void
    {
        $this->expectException(GameBoxException::class);

        $slug = 'missing-slug-test-132413';
        $this->repository->getOne($slug);
    }

    public function testGetAll(): void
    {
        $this->assertEquals(count($this->config), $this->repository->getAll()->count());
    }
}
