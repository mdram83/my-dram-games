<?php

namespace Games\Thousand;

use App\GameCore\GameSetup\GameSetupAbsFactory;
use App\Games\Thousand\GameSetupAbsFactoryThousand;
use App\Games\Thousand\GameSetupThousand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class GameSetupAbsFactoryThousandTest extends TestCase
{
    use RefreshDatabase;

    private GameSetupAbsFactoryThousand $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = App::make(GameSetupAbsFactoryThousand::class);
    }
    public function testInstance(): void
    {
        $this->assertInstanceOf(GameSetupAbsFactory::class, $this->factory);
    }

    public function testCreate(): void
    {
        $setup = $this->factory->create();
        $this->assertInstanceOf(GameSetupThousand::class, $setup);
    }
}
