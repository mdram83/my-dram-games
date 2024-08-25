<?php

namespace Tests\Feature\Games\TicTacToe;

use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameInvite\GameInviteRepository;
use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueForfeitAfter;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\GamePlay\GamePlay;
use App\GameCore\GamePlay\GamePlayBase;
use App\GameCore\GamePlay\GamePlayException;
use App\GameCore\GamePlay\GamePlayServicesProvider;
use App\GameCore\GamePlayStorage\Eloquent\GamePlayStorageEloquent;
use App\GameCore\GamePlayStorage\GamePlayStorage;
use App\GameCore\GameRecord\GameRecordRepository;
use App\GameCore\Services\Collection\Collection;
use App\Games\TicTacToe\Elements\GameBoardTicTacToe;
use App\Games\TicTacToe\GameMoveTicTacToe;
use App\Games\TicTacToe\GamePlayTicTacToe;
use App\Games\TicTacToe\GameResultTicTacToe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use MyDramGames\Utils\Exceptions\GameBoardException;
use MyDramGames\Utils\Player\Player;
use Tests\TestCase;

class GamePlayTicTacToeTest extends TestCase
{
    use RefreshDatabase;

    protected GamePlayTicTacToe $play;
    protected array $players;
    protected GameInvite $currentInvite;

    public function setUp(): void
    {
        parent::setUp();
        $this->players = [User::factory()->create(), User::factory()->create()];
        $this->play = $this->prepareGamePlayTicTacToe();
    }

    protected function prepareGameInvite(bool $allPlayers = true): GameInvite
    {
        $options = new CollectionGameOptionValueInput(
            App::make(Collection::class),
            [
                'numberOfPlayers' => GameOptionValueNumberOfPlayers::Players002,
                'autostart' => GameOptionValueAutostart::Disabled,
                'forfeitAfter' => GameOptionValueForfeitAfter::Disabled,
            ]
        );

        $invite = App::make(GameInviteFactory::class)->create('tic-tac-toe', $options, $this->players[0]);

        if ($allPlayers) {
            $invite->addPlayer($this->players[1]);
        }

        $this->currentInvite = $invite;

        return $invite;
    }

    protected function prepareGamePlayStorage(int|string $id = null): GamePlayStorage
    {
        return new GamePlayStorageEloquent(App::make(GameInviteRepository::class), $id);
    }

    protected function prepareGamePlayTicTacToe(int|string $id = null, bool $allPlayers = true, bool $finished = false): GamePlayTicTacToe
    {
        $storage = $this->prepareGamePlayStorage($id);
        $storage->setGameInvite($this->prepareGameInvite($allPlayers));
        if ($finished) {
            $storage->setFinished();
        }

        return new GamePlayTicTacToe($storage, App::make(GamePlayServicesProvider::class));
    }

    protected function prepareMove(?Player $overwritePlayer = null, int $fieldKey = 1): GameMoveTicTacToe
    {
        return new GameMoveTicTacToe($overwritePlayer ?? $this->players[0], $fieldKey);
    }

    public function testClassInstance(): void
    {
        $this->assertInstanceOf(GamePlay::class, $this->play);
        $this->assertInstanceOf(GamePlayBase::class, $this->play);
    }

    public function testThrowExceptionWhenCreatingWithNotAllPlayers(): void
    {
        $this->expectException(GamePlayException::class);
        $this->expectExceptionMessage(GamePlayException::MESSAGE_MISSING_PLAYERS);

        $this->prepareGamePlayTicTacToe(null, false);
    }

    public function testGetPlayers(): void
    {
        $players = $this->play->getPlayers();

        $this->assertTrue($players->exist($this->players[0]->getId()));
        $this->assertTrue($players->exist($this->players[1]->getId()));
    }

    public function testGetId(): void
    {
        $storage = $this->prepareGamePlayStorage();
        $play = $this->prepareGamePlayTicTacToe($storage->getId());

        $this->assertEquals($storage->getId(), $play->getId());
    }

    public function testGetPlayersFromLoadedObject(): void
    {
        $play = $this->prepareGamePlayTicTacToe($this->play->getId());
        $players = $play->getPlayers();

        $this->assertTrue($players->exist($this->players[0]->getId()));
        $this->assertTrue($players->exist($this->players[1]->getId()));
    }

    public function testGetGameInvite(): void
    {
        $this->assertSame($this->currentInvite, $this->play->getGameInvite());
    }

    public function testGetSituation(): void
    {
        $expected = [
            'players' => [$this->players[0]->getName(), $this->players[1]->getName()],
            'activePlayer' => $this->players[0]->getName(),
            'characters' => ['x' => $this->players[0]->getName(), 'o' => $this->players[1]->getName()],
            'board' => json_decode((new GameBoardTicTacToe())->toJson(), true),
            'isFinished' => false,
        ];

        $this->assertEquals($expected, $this->play->getSituation($this->players[0]));
        $this->assertEquals($expected, $this->play->getSituation($this->players[1]));
    }

    public function testThrowExceptionWhenHandlingMoveNotFromGame(): void
    {
        $this->expectException(GamePlayException::class);
        $this->expectExceptionMessage(GamePlayException::MESSAGE_INCOMPATIBLE_MOVE);

        $move = $this->createMock(GameMove::class);
        $move->method('getPlayer')->willReturn($this->players[0]);
        $move->method('getDetails')->willReturn(['fieldKey' => 1]);

        $this->play->handleMove($move);
    }

    public function testThrowExceptionWhenHandlingMoveOfNotCurrentPlayer(): void
    {
        $this->expectException(GamePlayException::class);
        $this->expectExceptionMessage(GamePlayException::MESSAGE_NOT_CURRENT_PLAYER);

        $move = $this->prepareMove($this->players[1]);
        $this->play->handleMove($move);
    }

    public function testThrowExceptionWhenHandlingMoveOfNotGamePlayer(): void
    {
        $this->expectException(GamePlayException::class);
        $this->expectExceptionMessage(GamePlayException::MESSAGE_NOT_CURRENT_PLAYER);

        $move = $this->prepareMove(User::factory()->create());
        $this->play->handleMove($move);
    }

    public function testHandleMoveBoardAndActivePlayerUpdated(): void
    {
        $this->play->handleMove($this->prepareMove());
        $situation = $this->play->getSituation($this->players[0]);

        $this->assertEquals($this->players[1]->getName(), $situation['activePlayer']);
        $this->assertNotNull($situation['board'][1]);
        $this->assertNull($situation['board'][2]);
    }

    public function testThrowExceptionWhenSettingSameFieldTwice(): void
    {
        $this->expectException(GameBoardException::class);
        $this->expectExceptionMessage(GameBoardException::MESSAGE_FIELD_ALREADY_SET);

        $move = $this->prepareMove();
        $this->play->handleMove($move);
        $move = $this->prepareMove($this->players[1]);
        $this->play->handleMove($move);
    }

    public function testGetSituationAfterMoveFromLoadedObject(): void
    {
        $move = $this->prepareMove();
        $this->play->handleMove($move);
        $play = $this->prepareGamePlayTicTacToe($this->play->getId());
        $situation = $play->getSituation($this->players[0]);

        $this->assertEquals($this->players[1]->getName(), $situation['activePlayer']);
        $this->assertNotNull($situation['board'][1]);
        $this->assertNull($situation['board'][2]);
    }

    public function testGetFinishedReturnFalsePriorToAnyMove(): void
    {
        $this->assertFalse($this->play->isFinished());
    }

    public function testThrowExceptionWhenHandlingMoveOnFinishedGame(): void
    {
        $this->expectException(GamePlayException::class);
        $this->expectExceptionMessage(GamePlayException::MESSAGE_MOVE_ON_FINISHED_GAME);

        $play = $this->prepareGamePlayTicTacToe(finished: true);
        $move = $this->prepareMove();
        $play->handleMove($move);
    }

    public function testGetFinishedReturnTrueAfterLastMove(): void
    {
        $play = $this->prepareGamePlayTicTacToe();
        $play->handleMove($this->prepareMove(null, 1));
        $play->handleMove($this->prepareMove($this->players[1], 5));
        $play->handleMove($this->prepareMove(null, 2));
        $play->handleMove($this->prepareMove($this->players[1], 9));
        $play->handleMove($this->prepareMove(null, 3));

        $this->assertTrue($play->isFinished());
    }

    public function testGetSituationContainResultAfterLastMove(): void
    {
        $play = $this->prepareGamePlayTicTacToe();
        $play->handleMove($this->prepareMove(null, 1));
        $play->handleMove($this->prepareMove($this->players[1], 5));
        $play->handleMove($this->prepareMove(null, 2));
        $play->handleMove($this->prepareMove($this->players[1], 9));
        $play->handleMove($this->prepareMove(null, 3));

        $expectedResult = new GameResultTicTacToe($this->players[0]->getName(), [1, 2, 3]);
        $expected = [
            'players' => [$this->players[0]->getName(), $this->players[1]->getName()],
            'activePlayer' => $this->players[1]->getName(),
            'characters' => ['x' => $this->players[0]->getName(), 'o' => $this->players[1]->getName()],
            'board' => [1 => 'x', 2 => 'x', 3 => 'x', 4 => null, 5 => 'o', 6 => null, 7 => null, 8 => null, 9 => 'o'],
            'isFinished' => true,
            'result' => [
                'details' => $expectedResult->getDetails(),
                'message' => $expectedResult->getMessage(),
            ],
        ];

        $this->assertEquals($expected, $play->getSituation($this->players[0]));
        $this->assertEquals($expected, $play->getSituation($this->players[1]));
    }

    public function testGameRecordsCreatedAfterLastMove(): void
    {
        $play = $this->prepareGamePlayTicTacToe();
        $play->handleMove($this->prepareMove(null, 1));
        $play->handleMove($this->prepareMove($this->players[1], 5));
        $play->handleMove($this->prepareMove(null, 2));
        $play->handleMove($this->prepareMove($this->players[1], 9));
        $play->handleMove($this->prepareMove(null, 3));
        $records = App::make(GameRecordRepository::class)->getByGameInvite($play->getGameInvite());

        $this->assertEquals(2, $records->count());
    }

    public function testThrowExceptionIfForfeitNotPlayer(): void
    {
        $this->expectException(GamePlayException::class);
        $this->expectExceptionMessage(GamePlayException::MESSAGE_NOT_PLAYER);

        $play = $this->prepareGamePlayTicTacToe();
        $play->handleForfeit(User::factory()->create());
    }

    public function testThrowExceptionWhenForfeitOnFinishedGame(): void
    {
        $this->expectException(GamePlayException::class);
        $this->expectExceptionMessage(GamePlayException::MESSAGE_MOVE_ON_FINISHED_GAME);

        $play = $this->prepareGamePlayTicTacToe();
        $play->handleMove($this->prepareMove(null, 1));
        $play->handleMove($this->prepareMove($this->players[1], 5));
        $play->handleMove($this->prepareMove(null, 2));
        $play->handleMove($this->prepareMove($this->players[1], 9));
        $play->handleMove($this->prepareMove(null, 3));
        $play->handleForfeit($this->players[0]);
    }

    public function testThrowExceptionWhenForfeitTwice(): void
    {
        $this->expectException(GamePlayException::class);
        $this->expectExceptionMessage(GamePlayException::MESSAGE_MOVE_ON_FINISHED_GAME);

        $play = $this->prepareGamePlayTicTacToe();
        $play->handleForfeit($this->players[0]);
        $play->handleForfeit($this->players[0]);
    }

    public function testGameRecordCreatedAfterForfeit(): void
    {
        $play = $this->prepareGamePlayTicTacToe();
        $play->handleForfeit($this->players[0]);
        $records = App::make(GameRecordRepository::class)->getByGameInvite($play->getGameInvite());

        $this->assertEquals(2, $records->count());
    }

    public function testGetSituationAfterForfeit(): void
    {
        $play = $this->prepareGamePlayTicTacToe();
        $play->handleForfeit($this->players[1]);

        $expectedResult = new GameResultTicTacToe($this->players[0]->getName(), [], true);
        $expected = [
            'players' => [$this->players[0]->getName(), $this->players[1]->getName()],
            'activePlayer' => $this->players[0]->getName(),
            'characters' => ['x' => $this->players[0]->getName(), 'o' => $this->players[1]->getName()],
            'board' => [1 => null, 2 => null, 3 => null, 4 => null, 5 => null, 6 => null, 7 => null, 8 => null, 9 => null],
            'isFinished' => true,
            'result' => [
                'details' => $expectedResult->getDetails(),
                'message' => $expectedResult->getMessage(),
            ],
        ];

        $this->assertEquals($expected, $play->getSituation($this->players[0]));
        $this->assertEquals($expected, $play->getSituation($this->players[1]));
    }

    public function testIsFinishedAfterForfeit(): void
    {
        $play = $this->prepareGamePlayTicTacToe();
        $play->handleForfeit($this->players[1]);

        $this->assertTrue($play->isFinished());
    }


}
