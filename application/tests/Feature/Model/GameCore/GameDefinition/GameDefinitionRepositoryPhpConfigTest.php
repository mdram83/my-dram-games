<?php

namespace Tests\Feature\Model\GameCore\GameDefinition;

use App\Models\GameCore\GameDefinition\GameDefinition;
use App\Models\GameCore\GameDefinition\GameDefinitionException;
use App\Models\GameCore\GameDefinition\GameDefinitionRepository;
use App\Models\GameCore\GameDefinition\GameDefinitionRepositoryPhpConfig;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class GameDefinitionRepositoryPhpConfigTest extends TestCase
{
    protected GameDefinitionRepositoryPhpConfig $repository;
    protected array $config;

    protected bool $commonSetup = false;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->commonSetup) {

            $this->repository = App::make(GameDefinitionRepositoryPhpConfig::class);
            $this->config = Config::get('games.gameDefinition');

            $this->commonSetup = true;
        }
    }

    public function testInstanceOfGameDefinitionRepository(): void
    {
        $this->assertInstanceOf(GameDefinitionRepository::class, $this->repository);
    }

    public function testGetOneExisting(): void
    {
        $slug = array_keys($this->config)[0];
        $gameDefinition = $this->repository->getOne($slug);
        $this->assertInstanceOf(GameDefinition::class, $gameDefinition);
    }

    public function testGetOneMissing(): void
    {
        $this->expectException(GameDefinitionException::class);
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
