<?php

namespace Tests\Feature\Games\TicTacToe;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameInvite\GameInviteRepository;
use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\GamePlay\GamePlay;
use App\GameCore\GamePlay\GamePlayBase;
use App\GameCore\GamePlay\GamePlayException;
use App\GameCore\GamePlayStorage\Eloquent\GamePlayStorageEloquent;
use App\GameCore\GamePlayStorage\GamePlayStorage;
use App\GameCore\Services\Collection\Collection;
use App\Games\TicTacToe\GameBoardTicTacToe;
use App\Games\TicTacToe\GamePlayTicTacToe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
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

    protected function prepareGamePlayTicTacToe(int|string $id = null, bool $allPlayers = true): GamePlayTicTacToe
    {
        $storage = $this->prepareGamePlayStorage($id);
        $storage->setGameInvite($this->prepareGameInvite($allPlayers));

        return new GamePlayTicTacToe(
            $storage,
            App::make(Collection::class),
        );
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
        ];

        $this->assertEquals($expected, $this->play->getSituation($this->players[0]));
        $this->assertEquals($expected, $this->play->getSituation($this->players[1]));
    }

}
