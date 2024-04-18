<?php

namespace Games\Thousand;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueForfeitAfter;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\GamePlay\GamePlay;
use App\GameCore\GamePlay\GamePlayBase;
use App\GameCore\GamePlay\GamePlayException;
use App\GameCore\GamePlay\GamePlayRepository;
use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\Collection;
use App\GameCore\Services\Collection\CollectionException;
use App\Games\Thousand\Elements\GamePhaseThousandSorting;
use App\Games\Thousand\GameOptionValueThousandBarrelPoints;
use App\Games\Thousand\GameOptionValueThousandNumberOfBombs;
use App\Games\Thousand\GameOptionValueThousandReDealConditions;
use App\Games\Thousand\GamePlayAbsFactoryThousand;
use App\Games\Thousand\GamePlayThousand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class GamePlayThousandTest extends TestCase
{
    use RefreshDatabase;

    private GamePlayThousand $play;
    private array $players;
    private GamePlayRepository $gamePlayRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->players = [
            User::factory()->create(),
            User::factory()->create(),
            User::factory()->create(),
            User::factory()->create(),
        ];

        $this->play = $this->getGamePlay($this->getGameInvite());
        $this->gamePlayRepository = App::make(GamePlayRepository::class);
    }

    protected function getGameInvite(bool $fourPlayers = false): GameInvite
    {
        $options = new CollectionGameOptionValueInput(
            App::make(Collection::class),
            [
                'numberOfPlayers' => $fourPlayers ? GameOptionValueNumberOfPlayers::Players004 : GameOptionValueNumberOfPlayers::Players003,
                'autostart' => GameOptionValueAutostart::Disabled,
                'forfeitAfter' => GameOptionValueForfeitAfter::Disabled,
                'thousand-barrel-points' => GameOptionValueThousandBarrelPoints::EightHundred,
                'thousand-number-of-bombs' => GameOptionValueThousandNumberOfBombs::One,
                'thousand-re-deal-conditions' => GameOptionValueThousandReDealConditions::Disabled,
            ]
        );

        $invite = App::make(GameInviteFactory::class)->create('thousand', $options, $this->players[0]);

        $invite->addPlayer($this->players[1]);
        $invite->addPlayer($this->players[2]);
        if ($fourPlayers) {
            $invite->addPlayer($this->players[3]);
        }

        return $invite;
    }

    protected function getGamePlay(GameInvite $invite): GamePlayThousand
    {
        return App::make(GamePlayAbsFactoryThousand::class)->create($invite);
    }

    public function testClassInstance(): void
    {
        $this->assertInstanceOf(GamePlay::class, $this->play);
        $this->assertInstanceOf(GamePlayBase::class, $this->play);
    }

    public function testGetSituationThrowExceptionIfNotPlayer(): void
    {
        $this->expectException(GamePlayException::class);
        $this->expectExceptionMessage(GamePlayException::MESSAGE_NOT_PLAYER);

        $this->play->getSituation(User::factory()->create());
    }

    public function testGetSituationAfterInitiationForThreePlayers(): void
    {
        $phase = new GamePhaseThousandSorting();
        $expectedPlayersNames = array_map(fn($player) => $player->getName(), $this->play->getPlayers()->toArray());
        $situation = $this->play->getSituation($this->players[0]);

        // three players available
        $this->assertCount(3, $situation['orderedPlayers']);
        $this->assertArrayHasKey($this->players[0]->getName(), $situation['orderedPlayers']);
        $this->assertArrayHasKey($this->players[1]->getName(), $situation['orderedPlayers']);
        $this->assertArrayHasKey($this->players[2]->getName(), $situation['orderedPlayers']);

        // player see his cards and not other players cards
        $this->assertCount(7, $situation['orderedPlayers'][$this->players[0]->getName()]['hand']);
        $this->assertEquals(7, $situation['orderedPlayers'][$this->players[1]->getName()]['hand']);
        $this->assertEquals(7, $situation['orderedPlayers'][$this->players[2]->getName()]['hand']);

        // player see his and other players tricks count but not cards
        $this->assertEquals(0, $situation['orderedPlayers'][$this->players[0]->getName()]['tricks']);
        $this->assertEquals(0, $situation['orderedPlayers'][$this->players[1]->getName()]['tricks']);
        $this->assertEquals(0, $situation['orderedPlayers'][$this->players[2]->getName()]['tricks']);

        // players see stock count but not cards
        $this->assertEquals(3, $situation['stock']);

        // all players barrel false
        $this->assertFalse($situation['orderedPlayers'][$this->players[0]->getName()]['barrel']);
        $this->assertFalse($situation['orderedPlayers'][$this->players[1]->getName()]['barrel']);
        $this->assertFalse($situation['orderedPlayers'][$this->players[2]->getName()]['barrel']);

        // all players points []
        $this->assertEquals([], $situation['orderedPlayers'][$this->players[0]->getName()]['points']);
        $this->assertEquals([], $situation['orderedPlayers'][$this->players[1]->getName()]['points']);
        $this->assertEquals([], $situation['orderedPlayers'][$this->players[2]->getName()]['points']);

        // table empty
        $this->assertEquals([], $situation['table']);

        // trump suit null
        $this->assertNull($situation['trumpSuit']);

        // bid winner null
        $this->assertNull($situation['bidWinner']);

        // bid amount 100
        $this->assertEquals(100, $situation['bidAmount']);

        // active player <> obligation <> dealer and within 3 players
        $this->assertTrue(in_array($situation['dealer'], $expectedPlayersNames));
        $this->assertTrue(in_array($situation['obligation'], $expectedPlayersNames));
        $this->assertTrue(in_array($situation['activePlayer'], $expectedPlayersNames));
        $this->assertNotEquals($situation['dealer'], $situation['obligation']);
        $this->assertNotEquals($situation['dealer'], $situation['activePlayer']);
        $this->assertNotEquals($situation['obligation'], $situation['activePlayer']);

        // round 1
        $this->assertEquals(1, $situation['round']);

        // phase attributes equal to specific phase methods (check 3)
        $this->assertEquals($phase->getKey(), $situation['phase']['key']);
        $this->assertEquals($phase->getName(), $situation['phase']['name']);
        $this->assertEquals($phase->getDescription(), $situation['phase']['description']);

        // is Finished false
        $this->assertFalse($situation['isFinished']);
    }

    public function testGetSituationAfterInitiationForFourPlayers(): void
    {
        $phase = new GamePhaseThousandSorting();
        $play = $this->getGamePlay($this->getGameInvite(true));
        $expectedPlayersNames = array_map(fn($player) => $player->getName(), $play->getPlayers()->toArray());
        $situation = $play->getSituation($this->players[0]);

        // three players available
        $this->assertCount(4, $situation['orderedPlayers']);
        $this->assertArrayHasKey($this->players[0]->getName(), $situation['orderedPlayers']);
        $this->assertArrayHasKey($this->players[1]->getName(), $situation['orderedPlayers']);
        $this->assertArrayHasKey($this->players[2]->getName(), $situation['orderedPlayers']);
        $this->assertArrayHasKey($this->players[3]->getName(), $situation['orderedPlayers']);

        // player see his cards and not other players cards
        $this->assertCount(
            $situation['dealer'] === $this->players[0]->getName() ? 0 : 7,
            $situation['orderedPlayers'][$this->players[0]->getName()]['hand']
        );
        $this->assertEquals(
            $situation['dealer'] === $this->players[1]->getName() ? 0 : 7,
            $situation['orderedPlayers'][$this->players[1]->getName()]['hand']
        );
        $this->assertEquals(
            $situation['dealer'] === $this->players[2]->getName() ? 0 : 7,
            $situation['orderedPlayers'][$this->players[2]->getName()]['hand']
        );
        $this->assertEquals(
            $situation['dealer'] === $this->players[3]->getName() ? 0 : 7,
            $situation['orderedPlayers'][$this->players[3]->getName()]['hand']
        );

        // player see his and other players tricks count but not cards
        $this->assertEquals(0, $situation['orderedPlayers'][$this->players[0]->getName()]['tricks']);
        $this->assertEquals(0, $situation['orderedPlayers'][$this->players[1]->getName()]['tricks']);
        $this->assertEquals(0, $situation['orderedPlayers'][$this->players[2]->getName()]['tricks']);
        $this->assertEquals(0, $situation['orderedPlayers'][$this->players[3]->getName()]['tricks']);

        // players see stock count but not cards
        $this->assertEquals(3, $situation['stock']);

        // all players barrel false
        $this->assertFalse($situation['orderedPlayers'][$this->players[0]->getName()]['barrel']);
        $this->assertFalse($situation['orderedPlayers'][$this->players[1]->getName()]['barrel']);
        $this->assertFalse($situation['orderedPlayers'][$this->players[2]->getName()]['barrel']);
        $this->assertFalse($situation['orderedPlayers'][$this->players[3]->getName()]['barrel']);

        // all players points []
        $this->assertEquals([], $situation['orderedPlayers'][$this->players[0]->getName()]['points']);
        $this->assertEquals([], $situation['orderedPlayers'][$this->players[1]->getName()]['points']);
        $this->assertEquals([], $situation['orderedPlayers'][$this->players[2]->getName()]['points']);
        $this->assertEquals([], $situation['orderedPlayers'][$this->players[3]->getName()]['points']);

        // table empty
        $this->assertEquals([], $situation['table']);

        // trump suit null
        $this->assertNull($situation['trumpSuit']);

        // bid winner null
        $this->assertNull($situation['bidWinner']);

        // bid amount 100
        $this->assertEquals(100, $situation['bidAmount']);

        // active player <> obligation <> dealer and within 3 players
        $this->assertTrue(in_array($situation['dealer'], $expectedPlayersNames));
        $this->assertTrue(in_array($situation['obligation'], $expectedPlayersNames));
        $this->assertTrue(in_array($situation['activePlayer'], $expectedPlayersNames));
        $this->assertNotEquals($situation['dealer'], $situation['obligation']);
        $this->assertNotEquals($situation['dealer'], $situation['activePlayer']);
        $this->assertNotEquals($situation['obligation'], $situation['activePlayer']);

        // round 1
        $this->assertEquals(1, $situation['round']);

        // phase attributes equal to specific phase methods (check 3)
        $this->assertEquals($phase->getKey(), $situation['phase']['key']);
        $this->assertEquals($phase->getName(), $situation['phase']['name']);
        $this->assertEquals($phase->getDescription(), $situation['phase']['description']);

        // is Finished false
        $this->assertFalse($situation['isFinished']);
    }

    public function testGetSituationAfterInitiationAndLoadingForThreePlayers(): void
    {
        $gamePlayId = $this->play->getId();
        $this->play = $this->gamePlayRepository->getOne($gamePlayId);

        $phase = new GamePhaseThousandSorting();
        $expectedPlayersNames = array_map(fn($player) => $player->getName(), $this->play->getPlayers()->toArray());
        $situation = $this->play->getSituation($this->players[0]);


        // three players available
        $this->assertCount(3, $situation['orderedPlayers']);
        $this->assertArrayHasKey($this->players[0]->getName(), $situation['orderedPlayers']);
        $this->assertArrayHasKey($this->players[1]->getName(), $situation['orderedPlayers']);
        $this->assertArrayHasKey($this->players[2]->getName(), $situation['orderedPlayers']);

        // player see his cards and not other players cards
        $this->assertCount(7, $situation['orderedPlayers'][$this->players[0]->getName()]['hand']);
        $this->assertEquals(7, $situation['orderedPlayers'][$this->players[1]->getName()]['hand']);
        $this->assertEquals(7, $situation['orderedPlayers'][$this->players[2]->getName()]['hand']);

        // player see his and other players tricks count but not cards
        $this->assertEquals(0, $situation['orderedPlayers'][$this->players[0]->getName()]['tricks']);
        $this->assertEquals(0, $situation['orderedPlayers'][$this->players[1]->getName()]['tricks']);
        $this->assertEquals(0, $situation['orderedPlayers'][$this->players[2]->getName()]['tricks']);

        // players see stock count but not cards
        $this->assertEquals(3, $situation['stock']);

        // all players barrel false
        $this->assertFalse($situation['orderedPlayers'][$this->players[0]->getName()]['barrel']);
        $this->assertFalse($situation['orderedPlayers'][$this->players[1]->getName()]['barrel']);
        $this->assertFalse($situation['orderedPlayers'][$this->players[2]->getName()]['barrel']);

        // all players points []
        $this->assertEquals([], $situation['orderedPlayers'][$this->players[0]->getName()]['points']);
        $this->assertEquals([], $situation['orderedPlayers'][$this->players[1]->getName()]['points']);
        $this->assertEquals([], $situation['orderedPlayers'][$this->players[2]->getName()]['points']);

        // table empty
        $this->assertEquals([], $situation['table']);

        // trump suit null
        $this->assertNull($situation['trumpSuit']);

        // bid winner null
        $this->assertNull($situation['bidWinner']);

        // bid amount 100
        $this->assertEquals(100, $situation['bidAmount']);

        // active player <> obligation <> dealer and within 3 players
        $this->assertTrue(in_array($situation['dealer'], $expectedPlayersNames));
        $this->assertTrue(in_array($situation['obligation'], $expectedPlayersNames));
        $this->assertTrue(in_array($situation['activePlayer'], $expectedPlayersNames));
        $this->assertNotEquals($situation['dealer'], $situation['obligation']);
        $this->assertNotEquals($situation['dealer'], $situation['activePlayer']);
        $this->assertNotEquals($situation['obligation'], $situation['activePlayer']);

        // round 1
        $this->assertEquals(1, $situation['round']);

        // phase attributes equal to specific phase methods (check 3)
        $this->assertEquals($phase->getKey(), $situation['phase']['key']);
        $this->assertEquals($phase->getName(), $situation['phase']['name']);
        $this->assertEquals($phase->getDescription(), $situation['phase']['description']);

        // is Finished false
        $this->assertFalse($situation['isFinished']);
    }
}
