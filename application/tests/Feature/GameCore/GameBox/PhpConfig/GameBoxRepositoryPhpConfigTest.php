<?php

namespace Tests\Feature\GameCore\GameBox\PhpConfig;

use App\GameCore\GameBox\GameBox;
use App\GameCore\GameBox\GameBoxException;
use App\GameCore\GameBox\GameBoxRepository;
use App\GameCore\GameBox\PhpConfig\GameBoxRepositoryPhpConfig;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class GameBoxRepositoryPhpConfigTest extends TestCase
{
    protected GameBoxRepositoryPhpConfig $repository;
    protected array $config;

    protected bool $commonSetup = false;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->commonSetup) {

            $this->repository = App::make(GameBoxRepositoryPhpConfig::class);
            $this->config = Config::get('games.box');

            $this->commonSetup = true;
        }
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
        $gameBoxList = $this->repository->getAll();
        $this->assertSameSize($this->config, $gameBoxList);
        foreach($gameBoxList as $gameBox) {
            $this->assertInstanceOf(GameBox::class, $gameBox);
        }
    }
}
