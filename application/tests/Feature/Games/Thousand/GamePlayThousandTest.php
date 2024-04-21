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
use App\GameCore\GamePlayStorage\GamePlayStorageRepository;
use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\Collection;
use App\Games\Thousand\Elements\GameMoveThousand;
use App\Games\Thousand\Elements\GameMoveThousandBidding;
use App\Games\Thousand\Elements\GameMoveThousandSorting;
use App\Games\Thousand\Elements\GamePhaseThousand;
use App\Games\Thousand\Elements\GamePhaseThousandBidding;
use App\Games\Thousand\GameMoveAbsFactoryThousand;
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
    private GamePhaseThousand $phase;
    private GameMoveAbsFactoryThousand $moveFactory;
    private GamePlayStorageRepository $storageRepository;

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
        $this->phase = new GamePhaseThousandBidding();
        $this->moveFactory = new GameMoveAbsFactoryThousand();
        $this->storageRepository = App::make(GamePlayStorageRepository::class);
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

    protected function getHand(Player $player): array
    {
        $situation = $this->play->getSituation($player);
        return $situation['orderedPlayers'][$player->getName()]['hand'];
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

        // all players ready true
        $this->assertTrue($situation['orderedPlayers'][$this->players[0]->getName()]['ready']);
        $this->assertTrue($situation['orderedPlayers'][$this->players[1]->getName()]['ready']);
        $this->assertTrue($situation['orderedPlayers'][$this->players[2]->getName()]['ready']);

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
        $this->assertEquals($this->phase->getKey(), $situation['phase']['key']);
        $this->assertEquals($this->phase->getName(), $situation['phase']['name']);
        $this->assertEquals($this->phase->getDescription(), $situation['phase']['description']);

        // is Finished false
        $this->assertFalse($situation['isFinished']);
    }

    public function testGetSituationAfterInitiationForFourPlayers(): void
    {
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

        // all players ready true
        $this->assertTrue($situation['orderedPlayers'][$this->players[0]->getName()]['ready']);
        $this->assertTrue($situation['orderedPlayers'][$this->players[1]->getName()]['ready']);
        $this->assertTrue($situation['orderedPlayers'][$this->players[2]->getName()]['ready']);
        $this->assertTrue($situation['orderedPlayers'][$this->players[3]->getName()]['ready']);

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
        $this->assertEquals($this->phase->getKey(), $situation['phase']['key']);
        $this->assertEquals($this->phase->getName(), $situation['phase']['name']);
        $this->assertEquals($this->phase->getDescription(), $situation['phase']['description']);

        // is Finished false
        $this->assertFalse($situation['isFinished']);
    }

    public function testGetSituationAfterInitiationAndLoadingForThreePlayers(): void
    {
        $gamePlayId = $this->play->getId();
        $this->play = $this->gamePlayRepository->getOne($gamePlayId);

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

        // all players ready true
        $this->assertTrue($situation['orderedPlayers'][$this->players[0]->getName()]['ready']);
        $this->assertTrue($situation['orderedPlayers'][$this->players[1]->getName()]['ready']);
        $this->assertTrue($situation['orderedPlayers'][$this->players[2]->getName()]['ready']);

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
        $this->assertEquals($this->phase->getKey(), $situation['phase']['key']);
        $this->assertEquals($this->phase->getName(), $situation['phase']['name']);
        $this->assertEquals($this->phase->getDescription(), $situation['phase']['description']);

        // is Finished false
        $this->assertFalse($situation['isFinished']);
    }

    public function testGetSituationAfterInitiationAndLoadingForFourPlayers(): void
    {
        $play = $this->getGamePlay($this->getGameInvite(true));
        $expectedPlayersNames = array_map(fn($player) => $player->getName(), $play->getPlayers()->toArray());

        $gamePlayId = $play->getId();
        $this->play = $this->gamePlayRepository->getOne($gamePlayId);

        $situation = $this->play->getSituation($this->players[0]);

        // four players available
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

        // all players ready true
        $this->assertTrue($situation['orderedPlayers'][$this->players[0]->getName()]['ready']);
        $this->assertTrue($situation['orderedPlayers'][$this->players[1]->getName()]['ready']);
        $this->assertTrue($situation['orderedPlayers'][$this->players[2]->getName()]['ready']);
        $this->assertTrue($situation['orderedPlayers'][$this->players[3]->getName()]['ready']);

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
        $this->assertEquals($this->phase->getKey(), $situation['phase']['key']);
        $this->assertEquals($this->phase->getName(), $situation['phase']['name']);
        $this->assertEquals($this->phase->getDescription(), $situation['phase']['description']);

        // is Finished false
        $this->assertFalse($situation['isFinished']);
    }

    public function testThrowExceptionHandleMoveOnFinishedGame(): void
    {
        $this->expectException(GamePlayException::class);
        $this->expectExceptionMessage(GamePlayException::MESSAGE_MOVE_ON_FINISHED_GAME);

        $this->storageRepository->getOne($this->play->getId())->setFinished();
        $this->play = $this->gamePlayRepository->getOne($this->play->getId());
        $this->play->handleMove($this->createMock(GameMoveThousand::class));
    }

    public function testThrowExceptionHandleMoveSortingInvalidCardKeys(): void
    {
        $this->expectException(GamePlayException::class);
        $this->expectExceptionMessage(GamePlayException::MESSAGE_INCOMPATIBLE_MOVE);

        $player = $this->players[0];
        $hand = $this->getHand($player);
        $hand[0] = 'ABC';
        $this->play->handleMove(new GameMoveThousandSorting($player, ['hand' => $hand]));
    }

    public function testThrowExceptionWhenHandleMoveSortingCardsNotMatchingHand(): void
    {
        $this->expectException(GamePlayException::class);
        $this->expectExceptionMessage(GamePlayException::MESSAGE_INCOMPATIBLE_MOVE);

        $player = $this->players[0];
        $hand = $this->getHand($player);
        $hand[0] = $this->getHand($this->players[1])[0];
        $this->play->handleMove(new GameMoveThousandSorting($player, ['hand' => $hand]));
    }

    public function testGetSituationAfterSortingCards(): void
    {
        $player = $this->players[0];
        $currentSituationPlayerOne = $this->play->getSituation($player);
        $currentSituationPlayerTwo = $this->play->getSituation($this->players[1]);
        $currentSituationPlayerThree = $this->play->getSituation($this->players[2]);

        $hand = $this->getHand($player);
        $currentKeys = array_values($hand);
        while ($currentKeys === array_values($hand)) {
            shuffle($hand);
        }
        $this->play->handleMove(new GameMoveThousandSorting($player, ['hand' => $hand]));

        $newSituationPlayerOne = $this->play->getSituation($player);
        $newSituationPlayerTwo = $this->play->getSituation($this->players[1]);
        $newSituationPlayerThree = $this->play->getSituation($this->players[2]);
        $newHand = $this->getHand($player);

        unset($currentSituationPlayerOne['orderedPlayers'][$player->getName()]['hand']);
        unset($currentSituationPlayerTwo['orderedPlayers'][$this->players[1]->getName()]['hand']);
        unset($currentSituationPlayerThree['orderedPlayers'][$this->players[2]->getName()]['hand']);
        unset($newSituationPlayerOne['orderedPlayers'][$player->getName()]['hand']);
        unset($newSituationPlayerTwo['orderedPlayers'][$this->players[1]->getName()]['hand']);
        unset($newSituationPlayerThree['orderedPlayers'][$this->players[2]->getName()]['hand']);

        $this->assertEquals($hand, $newHand);
        $this->assertEquals($currentSituationPlayerOne, $newSituationPlayerOne);
        $this->assertEquals($currentSituationPlayerTwo, $newSituationPlayerTwo);
        $this->assertEquals($currentSituationPlayerThree, $newSituationPlayerThree);
    }

    // BIDDING MOVE TESTS

    // exception not player turn
    public function testThrowExceptionWhenHandleMoveBiddingNotPlayerTurn(): void
    {
        $this->expectException(GamePlayException::class);
        $this->expectExceptionMessage(GamePlayException::MESSAGE_NOT_PLAYER);

        $player =
            $this->play->getSituation($this->players[0])['activePlayer'] === $this->players[0]->getName()
            ? $this->players[1] : $this->players[0];

        $this->play->handleMove(new GameMoveThousandBidding(
            $player,
            ['decision' => 'bid', 'bidAmount' => 110],
            new GamePhaseThousandBidding()
        ));
    }

    // exception bidding in wrong phase (active player, bid increased by 10, not passed before)
    // exception already passed
    // exception wrong bid amount (<> old + 10)
    // exception bid > 120 without mariage at hand
    // exception bid > 300
    // everyone pass, situation updated
    // second player pass, third win at 110
    // second bid 110, third win at 120
    // second bid 110, third bid at 120, first bid at 130 with mariage at hand
}
