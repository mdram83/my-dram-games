<?php

namespace Tests\Feature\Extensions\Core\GamePlay\Storage;

use App\Extensions\Core\GamePlay\Storage\GamePlayStorageEloquent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use MyDramGames\Core\Exceptions\GamePlayStorageException;
use MyDramGames\Core\GameInvite\GameInvite;
use MyDramGames\Core\GameInvite\GameInviteFactory;
use MyDramGames\Core\GameInvite\GameInviteRepository;
use MyDramGames\Core\GameOption\GameOptionConfigurationCollectionPowered;
use MyDramGames\Core\GameOption\GameOptionConfigurationGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueAutostartGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueForfeitAfterGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueNumberOfPlayersGeneric;
use MyDramGames\Core\GamePlay\Storage\GamePlayStorage;
use MyDramGames\Utils\Php\Collection\CollectionEngine;
use MyDramGames\Utils\Player\Player;
use Tests\TestCase;

class GamePlayStorageEloquentTest extends TestCase
{
    use RefreshDatabase;

    protected GamePlayStorageEloquent $storage;

    protected Player $playerOne;
    protected Player $playerTwo;

    protected GameInviteRepository $inviteRepository;
    protected GameInvite $inviteMock;
    protected GameInvite $invite;

    public function setUp(): void
    {
        parent::setUp();

        $this->playerOne = User::factory()->create();
        $this->playerTwo = User::factory()->create();

        $this->inviteRepository = App::make(GameInviteRepository::class);
        $this->inviteMock = $this->createMock(GameInvite::class);
        $this->inviteMock->method('getId')->willReturn('test-dummy-id-123');

        $this->invite = $this->prepareGameInvite();
        $this->storage = $this->constructStorageWithoutId();
    }

    protected function prepareGameInvite(): GameInvite
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
        $invite = $factory->create('tic-tac-toe', $options, $this->playerOne);
        $invite->addPlayer($this->playerTwo);

        return $invite;
    }

    protected function constructStorageWithId(int|string $id): GamePlayStorageEloquent
    {
        return new GamePlayStorageEloquent($this->inviteRepository, $id);
    }

    protected function constructStorageWithoutId(): GamePlayStorageEloquent
    {
        return new GamePlayStorageEloquent($this->inviteRepository);
    }

    public function testInstanceOfGamePlayStorage(): void
    {
        $this->assertInstanceOf(GamePlayStorage::class, $this->storage);
    }

    public function testGetId(): void
    {
        $this->assertIsString($this->storage->getId());
    }

    public function testCreateWithExistingId(): void
    {
        $id = $this->storage->getId();
        $this->assertEquals($id, $this->constructStorageWithId($id)->getId());
    }

    public function testThrowExceptionWhenCreatingWithMissingId(): void
    {
        $this->expectException(GamePlayStorageException::class);
        $this->expectExceptionMessage(GamePlayStorageException::MESSAGE_NOT_FOUND);

        $this->constructStorageWithId('definitely-missing-id');
    }

    public function testThrowExceptionWhenSettingGameInviteAlreadyUsedByOtherGamePlayStorage(): void
    {
        $this->expectException(GamePlayStorageException::class);
        $this->expectExceptionMessage(GamePlayStorageException::MESSAGE_INVALID_INVITE);

        $this->storage->setGameInvite($this->invite);
        $storage = $this->constructStorageWithoutId();
        $storage->setGameInvite($this->invite);
    }

    public function testSetGameInvite(): void
    {
        $this->storage->setGameInvite($this->invite);
        $this->assertEquals($this->invite->getId(), $this->storage->getGameInvite()->getId());
    }

    public function testThrowExceptionWhenGettingGameInviteNotSet(): void
    {
        $this->expectException(GamePlayStorageException::class);
        $this->expectExceptionMessage(GamePlayStorageException::MESSAGE_INVITE_NOT_SET);

        $this->storage->getGameInvite();
    }

    public function testGetGameInviteFromLoadedStorage(): void
    {
        $this->storage->setGameInvite($this->invite);
        $storage = $this->constructStorageWithId($this->storage->getId());
        $this->assertEquals($this->invite->getId(), $storage->getGameInvite()->getId());
    }

    public function testThrowExceptionWhenGettingIncorrectGameInviteFromLoadedStorage(): void
    {
        $this->expectException(GamePlayStorageException::class);
        $this->expectExceptionMessage(GamePlayStorageException::MESSAGE_INVALID_INVITE);

        $this->storage->setGameInvite($this->inviteMock);
        $storage = $this->constructStorageWithId($this->storage->getId());
        $storage->getGameInvite();
    }

    public function testThrowExceptionWhenOverwritingExistingSetup(): void
    {
        $this->expectException(GamePlayStorageException::class);
        $this->expectExceptionMessage(GamePlayStorageException::MESSAGE_SETUP_ALREADY_SET);

        $this->storage->setSetup();
        $this->storage->setSetup();
    }

    public function testGetSetupReturnFalseBeforeSetting(): void
    {
        $this->assertFalse($this->storage->getSetup());
    }

    public function testGetSetupAfterSettingUp(): void
    {
        $this->storage->setSetup();
        $this->assertTrue($this->storage->getSetup());
    }

    public function testGetSetupFromLoadedObject(): void
    {
        $this->storage->setSetup();
        $loaded = $this->constructStorageWithId($this->storage->getId());

        $this->assertTrue($loaded->getSetup());
    }

    public function testGetFinishedReturnFalseBeforeSetting(): void
    {
        $this->assertFalse($this->storage->getFinished());
    }

    public function testGetFinishedAfterSettingUp(): void
    {
        $this->storage->setFinished();
        $this->assertTrue($this->storage->getFinished());
    }

    public function testGetFinishedFromLoadedObject(): void
    {
        $this->storage->setFinished();
        $loaded = $this->constructStorageWithId($this->storage->getId());

        $this->assertTrue($loaded->getFinished());
    }

    public function testSetAndGetGameData(): void
    {
        $data = ['test-label' => 'test-value'];
        $this->storage->setGameData($data);

        $this->assertEquals($data, $this->storage->getGameData());
    }

    public function testSetAndGetDataFromLoadedObject(): void
    {
        $data = ['test-label' => 'test-value'];
        $this->storage->setGameData($data);
        $loaded = $this->constructStorageWithId($this->storage->getId());

        $this->assertEquals($data, $loaded->getGameData());
    }
}
