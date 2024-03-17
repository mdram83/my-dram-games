<?php

namespace Tests\Feature\Games\TicTacToe;

use App\GameCore\GameSetup\GameSetupAbsFactory;
use App\GameCore\Services\Collection\Collection;
use App\Games\TicTacToe\GameSetupAbsFactoryTicTacToe;
use App\Games\TicTacToe\GameSetupTicTacToe;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class GameSetupAbsFactoryTicTacToeTest extends TestCase
{
    protected Collection $collectionHandler;
    protected GameSetupAbsFactoryTicTacToe $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->collectionHandler = App::make(Collection::class);
        $this->factory = new GameSetupAbsFactoryTicTacToe($this->collectionHandler);
    }

    public function testInstanceOfGameSetupAbsFactory(): void
    {
        $this->assertInstanceOf(GameSetupAbsFactory::class, $this->factory);
    }

    public function testCreateWithoutOptions(): void
    {
        $this->assertInstanceOf(GameSetupTicTacToe::class, $this->factory->create());
    }
}
