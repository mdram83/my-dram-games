<?php

namespace Tests\Feature\GameCore\GameBox\PhpConfig;

use App\GameCore\GameBox\GameBox;
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
        $this->assertInstanceOf(\App\GameCore\GameBox\GameBoxRepository::class, $this->repository);
    }

    public function testGetOneExisting(): void
    {
        $slug = array_keys($this->config)[0];
        $gameDefinition = $this->repository->getOne($slug);
        $this->assertInstanceOf(\App\GameCore\GameBox\GameBox::class, $gameDefinition);
    }

    public function testGetOneMissing(): void
    {
        $this->expectException(\App\GameCore\GameBox\GameBoxException::class);
        $slug = 'missing-slug-test-132413';
        $this->repository->getOne($slug);
    }

    public function testGetAll(): void
    {
        $definitions = $this->repository->getAll();
        $this->assertEquals(count($this->config), count($definitions));
        foreach($definitions as $definition) {
            $this->assertInstanceOf(GameBox::class, $definition);
        }
    }
}
