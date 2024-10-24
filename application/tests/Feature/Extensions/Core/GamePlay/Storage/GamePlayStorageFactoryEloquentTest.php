<?php

namespace Tests\Feature\Extensions\Core\GamePlay\Storage;

use App\Extensions\Core\GamePlay\Storage\GamePlayStorageFactoryEloquent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use MyDramGames\Core\Exceptions\GamePlayStorageException;
use MyDramGames\Core\GameInvite\GameInvite;
use MyDramGames\Core\GameInvite\GameInviteFactory;
use MyDramGames\Core\GameOption\GameOptionConfigurationCollectionPowered;
use MyDramGames\Core\GameOption\GameOptionConfigurationGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueAutostartGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueForfeitAfterGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueNumberOfPlayersGeneric;
use MyDramGames\Core\GamePlay\Storage\GamePlayStorage;
use MyDramGames\Core\GamePlay\Storage\GamePlayStorageFactory;
use MyDramGames\Utils\Php\Collection\CollectionEngine;
use Tests\TestCase;

class GamePlayStorageFactoryEloquentTest extends TestCase
{
    use RefreshDatabase;

    protected GamePlayStorageFactoryEloquent $factory;
    protected GameInvite $invite;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = App::make(GamePlayStorageFactory::class);
        $this->prepareGameInvite();
    }

    protected function prepareGameInvite(): void
    {
        $options = new GameOptionConfigurationCollectionPowered(
            App::make(CollectionEngine::class),
            [
                new GameOptionConfigurationGeneric(
                    'numberOfPlayers',
                    GameOptionValueNumberOfPlayersGeneric::Players002
                ),
                new GameOptionConfigurationGeneric(
                    'autostart',
                    GameOptionValueAutostartGeneric::Disabled
                ),
                new GameOptionConfigurationGeneric(
                    'forfeitAfter',
                    GameOptionValueForfeitAfterGeneric::Disabled
                ),
            ]
        );

        $factory = App::make(GameInviteFactory::class);
        $this->invite = $factory->create('tic-tac-toe', $options, User::factory()->create());
        $this->invite->addPlayer(User::factory()->create());
    }

    public function testClassInstance(): void
    {
        $this->assertInstanceOf(GamePlayStorageFactory::class, $this->factory);
    }

    public function testThrowExceptionWhenUsingInviteAlreadyInUse(): void
    {
        $this->expectException(GamePlayStorageException::class);
        $this->expectExceptionMessage(GamePlayStorageException::MESSAGE_INVALID_INVITE);

        $this->factory->create($this->invite);
        $this->factory->create($this->invite);
    }

    public function testCreate(): void
    {
        $storage = $this->factory->create($this->invite);
        $this->assertInstanceOf(GamePlayStorage::class, $storage);
    }
}
