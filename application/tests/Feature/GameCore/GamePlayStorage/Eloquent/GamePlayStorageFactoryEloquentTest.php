<?php

namespace Tests\Feature\GameCore\GamePlayStorage\Eloquent;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueForfeitAfter;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\GamePlayStorage\Eloquent\GamePlayStorageFactoryEloquent;
use App\GameCore\GamePlayStorage\GamePlayStorage;
use App\GameCore\GamePlayStorage\GamePlayStorageException;
use App\GameCore\GamePlayStorage\GamePlayStorageFactory;
use App\GameCore\Services\Collection\Collection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
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
        $options = new CollectionGameOptionValueInput(
            App::make(Collection::class),
            [
                'numberOfPlayers' => GameOptionValueNumberOfPlayers::Players002,
                'autostart' => GameOptionValueAutostart::Disabled,
                'forfeitAfter' => GameOptionValueForfeitAfter::Disabled,
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
