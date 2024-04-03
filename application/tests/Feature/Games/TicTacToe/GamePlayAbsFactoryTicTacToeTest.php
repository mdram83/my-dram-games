<?php

namespace Tests\Feature\Games\TicTacToe;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueForfeitAfter;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\GamePlay\GamePlayAbsFactory;
use App\GameCore\GamePlay\GamePlayException;
use App\GameCore\GamePlayStorage\GamePlayStorage;
use App\GameCore\GamePlayStorage\GamePlayStorageException;
use App\GameCore\GamePlayStorage\GamePlayStorageFactory;
use App\GameCore\GameRecord\GameRecordFactory;
use App\GameCore\Services\Collection\Collection;
use App\Games\TicTacToe\GamePlayAbsFactoryTicTacToe;
use App\Games\TicTacToe\GamePlayTicTacToe;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class GamePlayAbsFactoryTicTacToeTest extends TestCase
{
    protected GamePlayAbsFactoryTicTacToe $factory;

    public function setUp(): void{
        parent::setUp();
        $this->factory = new GamePlayAbsFactoryTicTacToe(
            App::make(GamePlayStorageFactory::class),
            App::make(Collection::class),
            App::make(GameRecordFactory::class),
        );
    }

    protected function prepareGameInvite(bool $completeSetup = true): GameInvite
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
        $invite = $factory->create('tic-tac-toe', $options, User::factory()->create());

        if ($completeSetup) {
            $invite->addPlayer(User::factory()->create());
        }

        return $invite;
    }

    public function testClassInstance(): void
    {
        $this->assertInstanceOf(GamePlayAbsFactory::class, $this->factory);
    }

    public function testThrowExceptionWhenIncompleteInvite(): void
    {
        $this->expectException(GamePlayException::class);
        $this->expectExceptionMessage(GamePlayException::MESSAGE_MISSING_PLAYERS);

        $this->factory->create($this->prepareGameInvite(false));
    }

    public function testThrowExceptionWhenDuplicatedInvite(): void
    {
        $this->expectException(GamePlayStorageException::class);
        $this->expectExceptionMessage(GamePlayStorageException::MESSAGE_INVALID_INVITE);

        $invite = $this->prepareGameInvite();
        $this->factory->create($invite);
        $this->factory->create($invite);
    }

    public function testCreate(): void
    {
        $play = $this->factory->create($this->prepareGameInvite());
        $this->assertInstanceOf(GamePlayTicTacToe::class, $play);
    }
}
