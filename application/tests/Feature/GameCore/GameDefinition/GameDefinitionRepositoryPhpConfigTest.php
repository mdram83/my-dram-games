<?php

namespace Tests\Feature\GameCore\GameDefinition;

use App\GameCore\GameDefinition\GameDefinition;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class GameDefinitionRepositoryPhpConfigTest extends TestCase
{
    protected \App\GameCore\GameDefinition\PhPConfig\GameDefinitionRepositoryPhpConfig $repository;
    protected array $config;

    protected bool $commonSetup = false;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->commonSetup) {

            $this->repository = App::make(\App\GameCore\GameDefinition\PhPConfig\GameDefinitionRepositoryPhpConfig::class);
            $this->config = Config::get('games.gameDefinition');

            $this->commonSetup = true;
        }
    }

    public function testInstanceOfGameDefinitionRepository(): void
    {
        $this->assertInstanceOf(\App\GameCore\GameDefinition\GameDefinitionRepository::class, $this->repository);
    }

    public function testGetOneExisting(): void
    {
        $slug = array_keys($this->config)[0];
        $gameDefinition = $this->repository->getOne($slug);
        $this->assertInstanceOf(\App\GameCore\GameDefinition\GameDefinition::class, $gameDefinition);
    }

    public function testGetOneMissing(): void
    {
        $this->expectException(\App\GameCore\GameDefinition\GameDefinitionException::class);
        $slug = 'missing-slug-test-132413';
        $this->repository->getOne($slug);
    }

    public function testGetAll(): void
    {
        $definitions = $this->repository->getAll();
        $this->assertEquals(count($this->config), count($definitions));
        foreach($definitions as $definition) {
            $this->assertInstanceOf(GameDefinition::class, $definition);
        }
    }
}
