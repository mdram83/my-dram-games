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
use App\Games\Thousand\Elements\GameMoveThousandDeclaration;
use App\Games\Thousand\Elements\GameMoveThousandSorting;
use App\Games\Thousand\Elements\GameMoveThousandStockDistribution;
use App\Games\Thousand\Elements\GamePhaseThousand;
use App\Games\Thousand\Elements\GamePhaseThousandBidding;
use App\Games\Thousand\Elements\GamePhaseThousandCountPoints;
use App\Games\Thousand\Elements\GamePhaseThousandDeclaration;
use App\Games\Thousand\Elements\GamePhaseThousandPlayFirstCard;
use App\Games\Thousand\Elements\GamePhaseThousandStockDistribution;
use App\Games\Thousand\GameMoveAbsFactoryThousand;
use App\Games\Thousand\GameOptionValueThousandBarrelPoints;
use App\Games\Thousand\GameOptionValueThousandNumberOfBombs;
use App\Games\Thousand\GameOptionValueThousandReDealConditions;
use App\Games\Thousand\GamePlayAbsFactoryThousand;
use App\Games\Thousand\GamePlayThousand;
use App\Games\Thousand\GamePlayThousandException;
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

    protected function getDealNoMarriage(): array
    {
        return [
            ['A-H', 'K-H', 'J-H', '10-H', '9-H', 'A-S', 'K-S'],
            ['A-D', 'K-D', 'J-D', '10-D', '9-D', 'Q-S', 'J-S'],
            ['A-C', 'K-C', 'J-C', '10-C', '9-C', '10-S', '9-S'],
            ['Q-H', 'Q-D', 'Q-C'],
        ];
    }

    protected function getDealMarriages(): array
    {
        return [
            ['A-H', 'K-H', 'Q-H', 'J-H', '10-H', '9-H', 'A-S'],
            ['A-D', 'K-D', 'Q-D', 'J-D', '10-D', '9-D', 'K-S'],
            ['A-C', 'K-C', 'Q-C', 'J-C', '10-C', '9-C', 'Q-S'],
            ['J-S', '10-S', '9-S']
        ];
    }

    protected function getDealTwoAcesInStock(): array
    {
        return [
            ['Q-H', 'K-H', 'J-H', '10-H', '9-H', 'A-S', 'K-S'],
            ['Q-D', 'K-D', 'J-D', '10-D', '9-D', 'Q-S', 'J-S'],
            ['A-C', 'K-C', 'J-C', '10-C', '9-C', '10-S', '9-S'],
            ['A-H', 'A-D', 'Q-C'],
        ];
    }

    protected function getDealMarriageInStock(): array
    {
        return [
            ['A-H', 'K-H', 'J-H', '10-H', '9-H', 'A-S', 'Q-D'],
            ['A-D', 'K-D', 'J-D', '10-D', '9-D', 'Q-H', 'J-S'],
            ['A-C', 'K-C', 'J-C', '10-C', '9-C', '10-S', '9-S'],
            ['K-S', 'Q-S', 'Q-C'],
        ];
    }

    protected function updateGameData(array $overwrite): void
    {
        $storage = $this->storageRepository->getOne($this->play->getId());
        $storage->setGameData(array_merge($storage->getGameData(), $overwrite));
        $this->play = $this->gamePlayRepository->getOne($this->play->getId());
    }

    protected function updateGamePlayDeal(callable $getDeal): void
    {
        $storage = $this->storageRepository->getOne($this->play->getId());
        $data = $storage->getGameData();

        $numberOfPlayers = $this->play->getGameInvite()->getGameSetup()->getNumberOfPlayers()->getConfiguredValue()->getValue();

        if ($numberOfPlayers === 3) {
            $activePlayersNames = [
                $this->players[0]->getName(),
                $this->players[1]->getName(),
                $this->players[2]->getName(),
            ];
        } else {
            $activePlayers = array_filter($this->players, fn($player) => $player->getName() !== $data['dealer']);
            $activePlayersNames = array_values(array_map(fn($player) => $player->getName(), $activePlayers));
        }

        [$handOne, $handTwo, $handThree, $stock] = $getDeal();

        $data['orderedPlayers'][$activePlayersNames[0]]['hand'] = $handOne;
        $data['orderedPlayers'][$activePlayersNames[1]]['hand'] = $handTwo;
        $data['orderedPlayers'][$activePlayersNames[2]]['hand'] = $handThree;
        $data['stock'] = $stock;

        $storage->setGameData($data);
        $this->play = $this->gamePlayRepository->getOne($this->play->getId());
    }

    protected function getPlayerByName(string $playerName): Player
    {
        return array_values(array_filter($this->players, fn($player) => $player->getName() === $playerName))[0];
    }

    protected function processPhaseBidding(bool $fourPlayers = false, int $bidUntil = 110): void
    {
        if ($bidUntil >= 110) {
            for ($bidAmount = 110; $bidAmount <= $bidUntil; $bidAmount += 10) {
                $this->play->handleMove(new GameMoveThousandBidding(
                    $this->play->getActivePlayer(),
                    ['decision' => 'bid', 'bidAmount' => $bidAmount],
                    new GamePhaseThousandBidding()
                ));
            }
        }

        for ($i = 1; $i <= ($fourPlayers ? 4 : 3) - 1; $i++) {
            $this->play->handleMove(new GameMoveThousandBidding(
                $this->play->getActivePlayer(),
                ['decision' => 'pass'],
                new GamePhaseThousandBidding()
            ));
        }
    }

    protected function processPhaseStockDistribution(bool $fourPlayers = false): void
    {
        $bidWinnerName = $this->play->getSituation($this->players[0])['bidWinner'];
        $bidWinner = $this->getPlayerByName($bidWinnerName);
        $situation = $this->play->getSituation($bidWinner);

        $distributionPlayerNames = array_filter(
            array_keys($situation['orderedPlayers']),
            fn($playerName) => $playerName !== $bidWinnerName && (!$fourPlayers || $playerName !== $situation['dealer'])
        );

        $distributionPlayerName1 = array_pop($distributionPlayerNames);
        $distributionPlayerName2 = array_pop($distributionPlayerNames);

        $binWinnerHand = $situation['orderedPlayers'][$bidWinnerName]['hand'];
        $cards = (in_array('K-H', $binWinnerHand)
            ? ['J-H', '9-H']
            : (in_array('K-D', $binWinnerHand)
                ? ['J-D', '9-D']
                : ['J-C', '9-C']
            )
        );

        $distribution = ['distribution' => [
            $distributionPlayerName1 => $cards[0],
            $distributionPlayerName2 => $cards[1],
        ]];

        $this->play->handleMove(new GameMoveThousandStockDistribution(
            $bidWinner,
            $distribution,
            new GamePhaseThousandStockDistribution()
        ));
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

        // all players bombRounds []
        $this->assertEquals([], $situation['orderedPlayers'][$this->players[0]->getName()]['bombRounds']);
        $this->assertEquals([], $situation['orderedPlayers'][$this->players[1]->getName()]['bombRounds']);
        $this->assertEquals([], $situation['orderedPlayers'][$this->players[2]->getName()]['bombRounds']);

        // all players bid null, except obligation player bid 100
        $this->assertEquals(
            ($this->players[0]->getName() === $situation['obligation'] ? 100 : null),
            $situation['orderedPlayers'][$this->players[0]->getName()]['bid']
        );
        $this->assertEquals(
            ($this->players[1]->getName() === $situation['obligation'] ? 100 : null),
            $situation['orderedPlayers'][$this->players[1]->getName()]['bid']
        );
        $this->assertEquals(
            ($this->players[2]->getName() === $situation['obligation'] ? 100 : null),
            $situation['orderedPlayers'][$this->players[2]->getName()]['bid']
        );

        // all players have different seat position
        $this->assertIsInt($situation['orderedPlayers'][$this->players[0]->getName()]['seat']);
        $this->assertIsInt($situation['orderedPlayers'][$this->players[1]->getName()]['seat']);
        $this->assertIsInt($situation['orderedPlayers'][$this->players[2]->getName()]['seat']);
        $this->assertNotEquals(
            $situation['orderedPlayers'][$this->players[0]->getName()]['seat'],
            $situation['orderedPlayers'][$this->players[1]->getName()]['seat']
        );
        $this->assertNotEquals(
            $situation['orderedPlayers'][$this->players[0]->getName()]['seat'],
            $situation['orderedPlayers'][$this->players[2]->getName()]['seat']
        );
        $this->assertNotEquals(
            $situation['orderedPlayers'][$this->players[1]->getName()]['seat'],
            $situation['orderedPlayers'][$this->players[2]->getName()]['seat']
        );

        // seat position reflect player roles
        $this->assertEquals(1, $situation['orderedPlayers'][$situation['dealer']]['seat']);
        $this->assertEquals(2, $situation['orderedPlayers'][$situation['obligation']]['seat']);
        $this->assertEquals(3, $situation['orderedPlayers'][$situation['activePlayer']]['seat']);

        // table empty
        $this->assertEquals([], $situation['table']);

        // trump suit null
        $this->assertNull($situation['trumpSuit']);

        // bid winner null
        $this->assertNull($situation['bidWinner']);

        // bid amount 100
        $this->assertEquals(100, $situation['bidAmount']);

        // stockRecord empty
        $this->assertCount(0, $situation['stockRecord']);

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

        // all players bombRounds []
        $this->assertEquals([], $situation['orderedPlayers'][$this->players[0]->getName()]['bombRounds']);
        $this->assertEquals([], $situation['orderedPlayers'][$this->players[1]->getName()]['bombRounds']);
        $this->assertEquals([], $situation['orderedPlayers'][$this->players[2]->getName()]['bombRounds']);
        $this->assertEquals([], $situation['orderedPlayers'][$this->players[3]->getName()]['bombRounds']);

        // all players bid null, except obligation player bid 100
        $this->assertEquals(
            ($this->players[0]->getName() === $situation['obligation'] ? 100 : null),
            $situation['orderedPlayers'][$this->players[0]->getName()]['bid']
        );
        $this->assertEquals(
            ($this->players[1]->getName() === $situation['obligation'] ? 100 : null),
            $situation['orderedPlayers'][$this->players[1]->getName()]['bid']
        );
        $this->assertEquals(
            ($this->players[2]->getName() === $situation['obligation'] ? 100 : null),
            $situation['orderedPlayers'][$this->players[2]->getName()]['bid']
        );
        $this->assertEquals(
            ($this->players[3]->getName() === $situation['obligation'] ? 100 : null),
            $situation['orderedPlayers'][$this->players[3]->getName()]['bid']
        );

        // all players have different seat position
        $this->assertIsInt($situation['orderedPlayers'][$this->players[0]->getName()]['seat']);
        $this->assertIsInt($situation['orderedPlayers'][$this->players[1]->getName()]['seat']);
        $this->assertIsInt($situation['orderedPlayers'][$this->players[2]->getName()]['seat']);
        $this->assertNotEquals(
            $situation['orderedPlayers'][$this->players[0]->getName()]['seat'],
            $situation['orderedPlayers'][$this->players[1]->getName()]['seat']
        );
        $this->assertNotEquals(
            $situation['orderedPlayers'][$this->players[0]->getName()]['seat'],
            $situation['orderedPlayers'][$this->players[2]->getName()]['seat']
        );
        $this->assertNotEquals(
            $situation['orderedPlayers'][$this->players[1]->getName()]['seat'],
            $situation['orderedPlayers'][$this->players[2]->getName()]['seat']
        );
        $this->assertIsInt($situation['orderedPlayers'][$this->players[3]->getName()]['seat']);
        $this->assertNotEquals(
            $situation['orderedPlayers'][$this->players[0]->getName()]['seat'],
            $situation['orderedPlayers'][$this->players[3]->getName()]['seat']
        );
        $this->assertNotEquals(
            $situation['orderedPlayers'][$this->players[1]->getName()]['seat'],
            $situation['orderedPlayers'][$this->players[3]->getName()]['seat']
        );
        $this->assertNotEquals(
            $situation['orderedPlayers'][$this->players[2]->getName()]['seat'],
            $situation['orderedPlayers'][$this->players[3]->getName()]['seat']
        );

        // seat position reflect player roles
        $this->assertEquals(1, $situation['orderedPlayers'][$situation['dealer']]['seat']);
        $this->assertEquals(2, $situation['orderedPlayers'][$situation['obligation']]['seat']);
        $this->assertEquals(3, $situation['orderedPlayers'][$situation['activePlayer']]['seat']);

        // table empty
        $this->assertEquals([], $situation['table']);

        // trump suit null
        $this->assertNull($situation['trumpSuit']);

        // bid winner null
        $this->assertNull($situation['bidWinner']);

        // bid amount 100
        $this->assertEquals(100, $situation['bidAmount']);

        // stockRecord empty
        $this->assertCount(0, $situation['stockRecord']);

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

        // all players bombRounds []
        $this->assertEquals([], $situation['orderedPlayers'][$this->players[0]->getName()]['bombRounds']);
        $this->assertEquals([], $situation['orderedPlayers'][$this->players[1]->getName()]['bombRounds']);
        $this->assertEquals([], $situation['orderedPlayers'][$this->players[2]->getName()]['bombRounds']);

        // all players bid null, except obligation player bid 100
        $this->assertEquals(
            ($this->players[0]->getName() === $situation['obligation'] ? 100 : null),
            $situation['orderedPlayers'][$this->players[0]->getName()]['bid']
        );
        $this->assertEquals(
            ($this->players[1]->getName() === $situation['obligation'] ? 100 : null),
            $situation['orderedPlayers'][$this->players[1]->getName()]['bid']
        );
        $this->assertEquals(
            ($this->players[2]->getName() === $situation['obligation'] ? 100 : null),
            $situation['orderedPlayers'][$this->players[2]->getName()]['bid']
        );

        // all players have different seat position
        $this->assertIsInt($situation['orderedPlayers'][$this->players[0]->getName()]['seat']);
        $this->assertIsInt($situation['orderedPlayers'][$this->players[1]->getName()]['seat']);
        $this->assertIsInt($situation['orderedPlayers'][$this->players[2]->getName()]['seat']);
        $this->assertNotEquals(
            $situation['orderedPlayers'][$this->players[0]->getName()]['seat'],
            $situation['orderedPlayers'][$this->players[1]->getName()]['seat']
        );
        $this->assertNotEquals(
            $situation['orderedPlayers'][$this->players[0]->getName()]['seat'],
            $situation['orderedPlayers'][$this->players[2]->getName()]['seat']
        );
        $this->assertNotEquals(
            $situation['orderedPlayers'][$this->players[1]->getName()]['seat'],
            $situation['orderedPlayers'][$this->players[2]->getName()]['seat']
        );

        // seat position reflect player roles
        $this->assertEquals(1, $situation['orderedPlayers'][$situation['dealer']]['seat']);
        $this->assertEquals(2, $situation['orderedPlayers'][$situation['obligation']]['seat']);
        $this->assertEquals(3, $situation['orderedPlayers'][$situation['activePlayer']]['seat']);

        // table empty
        $this->assertEquals([], $situation['table']);

        // trump suit null
        $this->assertNull($situation['trumpSuit']);

        // bid winner null
        $this->assertNull($situation['bidWinner']);

        // bid amount 100
        $this->assertEquals(100, $situation['bidAmount']);

        // stockRecord empty
        $this->assertCount(0, $situation['stockRecord']);

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

        // all players bombRounds []
        $this->assertEquals([], $situation['orderedPlayers'][$this->players[0]->getName()]['bombRounds']);
        $this->assertEquals([], $situation['orderedPlayers'][$this->players[1]->getName()]['bombRounds']);
        $this->assertEquals([], $situation['orderedPlayers'][$this->players[2]->getName()]['bombRounds']);
        $this->assertEquals([], $situation['orderedPlayers'][$this->players[3]->getName()]['bombRounds']);

        // all players bid null, except obligation player bid 100
        $this->assertEquals(
            ($this->players[0]->getName() === $situation['obligation'] ? 100 : null),
            $situation['orderedPlayers'][$this->players[0]->getName()]['bid']
        );
        $this->assertEquals(
            ($this->players[1]->getName() === $situation['obligation'] ? 100 : null),
            $situation['orderedPlayers'][$this->players[1]->getName()]['bid']
        );
        $this->assertEquals(
            ($this->players[2]->getName() === $situation['obligation'] ? 100 : null),
            $situation['orderedPlayers'][$this->players[2]->getName()]['bid']
        );
        $this->assertEquals(
            ($this->players[3]->getName() === $situation['obligation'] ? 100 : null),
            $situation['orderedPlayers'][$this->players[3]->getName()]['bid']
        );

        // all players have different seat position
        $this->assertIsInt($situation['orderedPlayers'][$this->players[0]->getName()]['seat']);
        $this->assertIsInt($situation['orderedPlayers'][$this->players[1]->getName()]['seat']);
        $this->assertIsInt($situation['orderedPlayers'][$this->players[2]->getName()]['seat']);
        $this->assertNotEquals(
            $situation['orderedPlayers'][$this->players[0]->getName()]['seat'],
            $situation['orderedPlayers'][$this->players[1]->getName()]['seat']
        );
        $this->assertNotEquals(
            $situation['orderedPlayers'][$this->players[0]->getName()]['seat'],
            $situation['orderedPlayers'][$this->players[2]->getName()]['seat']
        );
        $this->assertNotEquals(
            $situation['orderedPlayers'][$this->players[1]->getName()]['seat'],
            $situation['orderedPlayers'][$this->players[2]->getName()]['seat']
        );
        $this->assertIsInt($situation['orderedPlayers'][$this->players[3]->getName()]['seat']);
        $this->assertNotEquals(
            $situation['orderedPlayers'][$this->players[0]->getName()]['seat'],
            $situation['orderedPlayers'][$this->players[3]->getName()]['seat']
        );
        $this->assertNotEquals(
            $situation['orderedPlayers'][$this->players[1]->getName()]['seat'],
            $situation['orderedPlayers'][$this->players[3]->getName()]['seat']
        );
        $this->assertNotEquals(
            $situation['orderedPlayers'][$this->players[2]->getName()]['seat'],
            $situation['orderedPlayers'][$this->players[3]->getName()]['seat']
        );

        // seat position reflect player roles
        $this->assertEquals(1, $situation['orderedPlayers'][$situation['dealer']]['seat']);
        $this->assertEquals(2, $situation['orderedPlayers'][$situation['obligation']]['seat']);
        $this->assertEquals(3, $situation['orderedPlayers'][$situation['activePlayer']]['seat']);

        // table empty
        $this->assertEquals([], $situation['table']);

        // trump suit null
        $this->assertNull($situation['trumpSuit']);

        // bid winner null
        $this->assertNull($situation['bidWinner']);

        // bid amount 100
        $this->assertEquals(100, $situation['bidAmount']);

        // stockRecord empty
        $this->assertCount(0, $situation['stockRecord']);

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

    public function testThrowExceptionWhenHandleMoveBiddingNotPlayerTurn(): void
    {
        $this->expectException(GamePlayException::class);
        $this->expectExceptionMessage(GamePlayException::MESSAGE_NOT_CURRENT_PLAYER);

        $player = $this->play->getActivePlayer()->getId() === $this->players[0]->getId() ? $this->players[1] : $this->players[0];

        $this->play->handleMove(new GameMoveThousandBidding(
            $player,
            ['decision' => 'bid', 'bidAmount' => 110],
            new GamePhaseThousandBidding()
        ));
    }

    public function testThrowExceptionWhenHandleMoveOtherThanSortingMoveInWrongPhase(): void
    {
        $this->expectException(GamePlayException::class);
        $this->expectExceptionMessage(GamePlayException::MESSAGE_INCOMPATIBLE_MOVE);

        $storage = $this->storageRepository->getOne($this->play->getId());
        $data = $storage->getGameData();
        $phase = new GamePhaseThousandDeclaration();
        $data['phase'] = [
            'key' => $phase->getKey(),
            'name' => $phase->getName(),
            'description' => $phase->getDescription(),
        ];

        $storage->setGameData($data);
        $this->play = $this->gamePlayRepository->getOne($this->play->getId());

        $this->play->handleMove(new GameMoveThousandBidding(
            $this->play->getActivePlayer(),
            ['decision' => 'bid', 'bidAmount' => 110],
            new GamePhaseThousandBidding()
        ));
    }

    public function testThrowExceptionBidStepOtherThanTen(): void
    {
        $this->expectException(GamePlayThousandException::class);
        $this->expectExceptionMessage(GamePlayThousandException::MESSAGE_RULE_BID_STEP_INVALID);

        $player = $this->play->getActivePlayer();
        $bidAmount = $this->play->getSituation($player)['bidAmount'] + 11;

        $this->play->handleMove(new GameMoveThousandBidding(
            $player,
            ['decision' => 'bid', 'bidAmount' => $bidAmount],
            new GamePhaseThousandBidding()
        ));
    }

    public function testThrowExceptionBidOver120WithoutMarriageAtHand(): void
    {
        $this->expectException(GamePlayThousandException::class);
        $this->expectExceptionMessage(GamePlayThousandException::MESSAGE_RULE_BID_NO_MARRIAGE);

        $this->updateGamePlayDeal([$this, 'getDealNoMarriage']);

        for ($i = 110; $i <= 130; $i = $i + 10) {
            $this->play->handleMove(new GameMoveThousandBidding(
                $this->play->getActivePlayer(),
                ['decision' => 'bid', 'bidAmount' => $i],
                new GamePhaseThousandBidding()
            ));
        }
    }

    public function testThrowExceptionWhenBiddingAfterPassing(): void
    {
        $this->expectException(GamePlayException::class);
        $this->expectExceptionMessage(GamePlayException::MESSAGE_NOT_CURRENT_PLAYER);

        $this->updateGamePlayDeal([$this, 'getDealMarriages']);

        $player2pass = $this->play->getActivePlayer();
        $this->play->handleMove(new GameMoveThousandBidding(
            $player2pass,
            ['decision' => 'pass'],
            new GamePhaseThousandBidding()
        ));

        $player3bid = $this->play->getActivePlayer();
        $this->play->handleMove(new GameMoveThousandBidding(
            $player3bid,
            ['decision' => 'bid', 'bidAmount' => 110],
            new GamePhaseThousandBidding()
        ));

        $player1bid = $this->play->getActivePlayer();
        $this->play->handleMove(new GameMoveThousandBidding(
            $player1bid,
            ['decision' => 'bid', 'bidAmount' => 120],
            new GamePhaseThousandBidding()
        ));

        $this->play->handleMove(new GameMoveThousandBidding(
            $player2pass,
            ['decision' => 'bid', 'bidAmount' => 130],
            new GamePhaseThousandBidding()
        ));
    }

    public function testGetSituationAfterBiddingFinishedAt300(): void
    {
        $this->updateGamePlayDeal([$this, 'getDealMarriages']);

        $initialSituation0 = $this->play->getSituation($this->players[0]);
        $initialSituation1 = $this->play->getSituation($this->players[1]);
        $initialSituation2 = $this->play->getSituation($this->players[2]);

        for ($i = 110; $i <= 300; $i = $i + 10) {
            $bidWinner = $this->play->getActivePlayer();

            $lastBiddingSituation0 = $this->play->getSituation($this->players[0]);
            $lastBiddingSituation1 = $this->play->getSituation($this->players[1]);
            $lastBiddingSituation2 = $this->play->getSituation($this->players[2]);
            $lastBiddingPhase = $lastBiddingSituation0['phase'];
            $lastBiddingBidAmount = $lastBiddingSituation0['bidAmount'];

            $this->play->handleMove(new GameMoveThousandBidding(
                $bidWinner,
                ['decision' => 'bid', 'bidAmount' => $i],
                new GamePhaseThousandBidding()
            ));
        }

        $setsToRemoveIncomparableData = [
            &$initialSituation0,
            &$initialSituation1,
            &$initialSituation2,
            &$lastBiddingSituation0,
            &$lastBiddingSituation1,
            &$lastBiddingSituation2,
        ];

        foreach ($setsToRemoveIncomparableData as &$set) {
            unset($set['bidWinner'], $set['bidAmount'], $set['activePlayer']);
            foreach ($set['orderedPlayers'] as &$orderedPlayer) {
                unset($orderedPlayer['bid']);
            }
        }

        $finalSituation0 = $this->play->getSituation($this->players[0]);
        $finalSituation1 = $this->play->getSituation($this->players[1]);
        $finalSituation2 = $this->play->getSituation($this->players[2]);

        $bidWinnerHand = $this->play->getSituation($bidWinner)['orderedPlayers'][$bidWinner->getName()]['hand'];

        $this->assertEquals((new GamePhaseThousandBidding())->getKey(), $lastBiddingPhase['key']);
        $this->assertEquals(290, $lastBiddingBidAmount);
        $this->assertEquals($initialSituation0, $lastBiddingSituation0);
        $this->assertEquals($initialSituation1, $lastBiddingSituation1);
        $this->assertEquals($initialSituation2, $lastBiddingSituation2);

        $this->assertEquals((new GamePhaseThousandStockDistribution())->getKey(), $finalSituation0['phase']['key']);
        $this->assertEquals($bidWinner->getName(), $finalSituation0['bidWinner']);
        $this->assertEquals(300, $finalSituation0['bidAmount']);
        $this->assertEquals(0, $finalSituation0['stock']);
        $this->assertEquals($bidWinner->getName(), $finalSituation0['activePlayer']);
        $this->assertCount(0, $lastBiddingSituation0['stockRecord']);
        $this->assertCount(3, $finalSituation0['stockRecord']);
        $this->assertCount(0, array_diff($finalSituation0['stockRecord'], $bidWinnerHand));
        $this->assertNull($finalSituation0['orderedPlayers'][$this->players[0]->getName()]['bid']);
        $this->assertNull($finalSituation0['orderedPlayers'][$this->players[1]->getName()]['bid']);
        $this->assertNull($finalSituation0['orderedPlayers'][$this->players[2]->getName()]['bid']);
        $this->assertNull($finalSituation1['orderedPlayers'][$this->players[0]->getName()]['bid']);
        $this->assertNull($finalSituation1['orderedPlayers'][$this->players[1]->getName()]['bid']);
        $this->assertNull($finalSituation1['orderedPlayers'][$this->players[2]->getName()]['bid']);
        $this->assertNull($finalSituation2['orderedPlayers'][$this->players[0]->getName()]['bid']);
        $this->assertNull($finalSituation2['orderedPlayers'][$this->players[1]->getName()]['bid']);
        $this->assertNull($finalSituation2['orderedPlayers'][$this->players[2]->getName()]['bid']);

        $this->assertCount(
            $bidWinner->getName() === $this->players[0]->getName() ? 10 : 7,
            $finalSituation0['orderedPlayers'][$this->players[0]->getName()]['hand']
        );
        $this->assertCount(
            $bidWinner->getName() === $this->players[1]->getName() ? 10 : 7,
            $finalSituation1['orderedPlayers'][$this->players[1]->getName()]['hand']
        );
        $this->assertCount(
            $bidWinner->getName() === $this->players[2]->getName() ? 10 : 7,
            $finalSituation2['orderedPlayers'][$this->players[2]->getName()]['hand']
        );
    }

    public function testGetSituationAfterBiddingFinishedNoBidIncrease(): void
    {
        for ($i = 1; $i <= 2; $i++) {
            $this->play->handleMove(new GameMoveThousandBidding(
                $this->play->getActivePlayer(),
                ['decision' => 'pass'],
                new GamePhaseThousandBidding()
            ));
        }
        $situation = $this->play->getSituation($this->players[0]);

        $this->assertEquals((new GamePhaseThousandStockDistribution())->getKey(), $situation['phase']['key']);
        $this->assertEquals(0, $situation['stock']);
        $this->assertCount(0, $situation['stockRecord']);
        $this->assertEquals(100, $situation['bidAmount']);
        $this->assertEquals($situation['obligation'], $situation['bidWinner']);
        $this->assertEquals($situation['obligation'], $situation['activePlayer']);

        $this->assertEquals(
            $situation['obligation'] === $this->players[0]->getName() ? 10 : 7,
            count($this->play->getSituation($this->players[0])['orderedPlayers'][$this->players[0]->getName()]['hand'])
        );
        $this->assertEquals(
            $situation['obligation'] === $this->players[1]->getName() ? 10 : 7,
            count($this->play->getSituation($this->players[1])['orderedPlayers'][$this->players[1]->getName()]['hand'])
        );
        $this->assertEquals(
            $situation['obligation'] === $this->players[2]->getName() ? 10 : 7,
            count($this->play->getSituation($this->players[2])['orderedPlayers'][$this->players[2]->getName()]['hand'])
        );
    }

    public function testGetSituationAfterBiddingThirdWinAt110(): void
    {
        $this->play->handleMove(new GameMoveThousandBidding(
            $this->play->getActivePlayer(),
            ['decision' => 'pass'],
            new GamePhaseThousandBidding()
        ));

        $bidWinner = $this->play->getActivePlayer();
        $this->play->handleMove(new GameMoveThousandBidding(
            $bidWinner,
            ['decision' => 'bid', 'bidAmount' => 110],
            new GamePhaseThousandBidding()
        ));

        $this->play->handleMove(new GameMoveThousandBidding(
            $this->play->getActivePlayer(),
            ['decision' => 'pass'],
            new GamePhaseThousandBidding()
        ));

        $situation = $this->play->getSituation($bidWinner);

        $this->assertEquals((new GamePhaseThousandStockDistribution())->getKey(), $situation['phase']['key']);
        $this->assertEquals(0, $situation['stock']);
        $this->assertCount(3, $situation['stockRecord']);
        $this->assertEquals(110, $situation['bidAmount']);
        $this->assertNotEquals($situation['obligation'], $situation['bidWinner']);
        $this->assertCount(10, $situation['orderedPlayers'][$bidWinner->getName()]['hand']);
        $this->assertEquals($bidWinner->getName(), $situation['activePlayer']);

        $this->assertEquals(
            $situation['bidWinner'] === $this->players[0]->getName() ? 10 : 7,
            count($this->play->getSituation($this->players[0])['orderedPlayers'][$this->players[0]->getName()]['hand'])
        );
        $this->assertEquals(
            $situation['bidWinner'] === $this->players[1]->getName() ? 10 : 7,
            count($this->play->getSituation($this->players[1])['orderedPlayers'][$this->players[1]->getName()]['hand'])
        );
        $this->assertEquals(
            $situation['bidWinner'] === $this->players[2]->getName() ? 10 : 7,
            count($this->play->getSituation($this->players[2])['orderedPlayers'][$this->players[2]->getName()]['hand'])
        );
    }

    public function testGetSituationAfterBiddingThirdWinAt120(): void
    {
        $this->play->handleMove(new GameMoveThousandBidding(
            $this->play->getActivePlayer(),
            ['decision' => 'bid', 'bidAmount' => 110],
            new GamePhaseThousandBidding()
        ));

        $bidWinner = $this->play->getActivePlayer();
        $this->play->handleMove(new GameMoveThousandBidding(
            $bidWinner,
            ['decision' => 'bid', 'bidAmount' => 120],
            new GamePhaseThousandBidding()
        ));

        $this->play->handleMove(new GameMoveThousandBidding(
            $this->play->getActivePlayer(),
            ['decision' => 'pass'],
            new GamePhaseThousandBidding()
        ));

        $this->play->handleMove(new GameMoveThousandBidding(
            $this->play->getActivePlayer(),
            ['decision' => 'pass'],
            new GamePhaseThousandBidding()
        ));

        $situation = $this->play->getSituation($bidWinner);

        $this->assertEquals((new GamePhaseThousandStockDistribution())->getKey(), $situation['phase']['key']);
        $this->assertEquals(0, $situation['stock']);
        $this->assertCount(3, $situation['stockRecord']);
        $this->assertEquals(120, $situation['bidAmount']);
        $this->assertNotEquals($situation['obligation'], $situation['bidWinner']);
        $this->assertCount(10, $situation['orderedPlayers'][$bidWinner->getName()]['hand']);
        $this->assertEquals($bidWinner->getName(), $situation['activePlayer']);

        $this->assertEquals(
            $situation['bidWinner'] === $this->players[0]->getName() ? 10 : 7,
            count($this->play->getSituation($this->players[0])['orderedPlayers'][$this->players[0]->getName()]['hand'])
        );
        $this->assertEquals(
            $situation['bidWinner'] === $this->players[1]->getName() ? 10 : 7,
            count($this->play->getSituation($this->players[1])['orderedPlayers'][$this->players[1]->getName()]['hand'])
        );
        $this->assertEquals(
            $situation['bidWinner'] === $this->players[2]->getName() ? 10 : 7,
            count($this->play->getSituation($this->players[2])['orderedPlayers'][$this->players[2]->getName()]['hand'])
        );
    }

    public function testGetSituationAfterBiddingFirstWinAt130(): void
    {

        $this->updateGamePlayDeal([$this, 'getDealMarriages']);

        $this->play->handleMove(new GameMoveThousandBidding(
            $this->play->getActivePlayer(),
            ['decision' => 'bid', 'bidAmount' => 110],
            new GamePhaseThousandBidding()
        ));

        $this->play->handleMove(new GameMoveThousandBidding(
            $this->play->getActivePlayer(),
            ['decision' => 'bid', 'bidAmount' => 120],
            new GamePhaseThousandBidding()
        ));

        $bidWinner = $this->play->getActivePlayer();
        $this->play->handleMove(new GameMoveThousandBidding(
            $bidWinner,
            ['decision' => 'bid', 'bidAmount' => 130],
            new GamePhaseThousandBidding()
        ));

        $this->play->handleMove(new GameMoveThousandBidding(
            $this->play->getActivePlayer(),
            ['decision' => 'pass'],
            new GamePhaseThousandBidding()
        ));

        $this->play->handleMove(new GameMoveThousandBidding(
            $this->play->getActivePlayer(),
            ['decision' => 'pass'],
            new GamePhaseThousandBidding()
        ));

        $situation = $this->play->getSituation($bidWinner);

        $this->assertEquals((new GamePhaseThousandStockDistribution())->getKey(), $situation['phase']['key']);
        $this->assertEquals(0, $situation['stock']);
        $this->assertCount(3, $situation['stockRecord']);
        $this->assertEquals(130, $situation['bidAmount']);
        $this->assertEquals($situation['obligation'], $situation['bidWinner']);
        $this->assertCount(10, $situation['orderedPlayers'][$bidWinner->getName()]['hand']);

        $this->assertEquals(
            $situation['bidWinner'] === $this->players[0]->getName() ? 10 : 7,
            count($this->play->getSituation($this->players[0])['orderedPlayers'][$this->players[0]->getName()]['hand'])
        );
        $this->assertEquals(
            $situation['bidWinner'] === $this->players[1]->getName() ? 10 : 7,
            count($this->play->getSituation($this->players[1])['orderedPlayers'][$this->players[1]->getName()]['hand'])
        );
        $this->assertEquals(
            $situation['bidWinner'] === $this->players[2]->getName() ? 10 : 7,
            count($this->play->getSituation($this->players[2])['orderedPlayers'][$this->players[2]->getName()]['hand'])
        );
    }

    public function testGetSituationAfterBiddingFewTimesFourPlayers(): void
    {
        $this->play = $this->getGamePlay($this->getGameInvite(true));
        $this->updateGamePlayDeal([$this, 'getDealMarriages']);

        $activePlayersNames = [];
        for ($i = 110; $i <= 200; $i += 10) {
            $activePlayer = $this->play->getActivePlayer();
            $activePlayersNames[] = $activePlayer->getName();
            $this->play->handleMove(new GameMoveThousandBidding(
                $activePlayer,
                ['decision' => 'bid', 'bidAmount' => $i],
                new GamePhaseThousandBidding()
            ));
        }

        $dealerName = $this->play->getSituation($this->play->getActivePlayer())['dealer'];

        $this->assertFalse(in_array($dealerName, $activePlayersNames, true));
    }

    public function testThrowExceptionWhenHandleMoveStockDistributionToSelf(): void
    {
        $this->expectException(GamePlayThousandException::class);
        $this->expectExceptionMessage(GamePlayThousandException::MESSAGE_INCOMPATIBLE_MOVE);

        $this->updateGamePlayDeal([$this, 'getDealNoMarriage']);
        $this->processPhaseBidding();

        $bidWinnerName = $this->play->getSituation($this->players[0])['bidWinner'];
        $bidWinner = $this->getPlayerByName($bidWinnerName);
        $situation = $this->play->getSituation($bidWinner);

        $distribution = ['distribution' => [
            $bidWinnerName => $situation['orderedPlayers'][$bidWinnerName]['hand'][0],
            (
                $this->players[0]->getName() !== $bidWinnerName
                    ? $this->players[0]->getName()
                    : $this->players[1]->getName()
            ) => $situation['orderedPlayers'][$bidWinnerName]['hand'][1],
        ]];

        $this->play->handleMove(new GameMoveThousandStockDistribution(
            $bidWinner,
            $distribution,
            new GamePhaseThousandStockDistribution()
        ));
    }

    public function testThrowExceptionWhenHandleMoveStockDistributionSameCard(): void
    {
        $this->expectException(GamePlayThousandException::class);
        $this->expectExceptionMessage(GamePlayThousandException::MESSAGE_INCOMPATIBLE_MOVE);

        $this->updateGamePlayDeal([$this, 'getDealNoMarriage']);
        $this->processPhaseBidding();

        $bidWinnerName = $this->play->getSituation($this->players[0])['bidWinner'];
        $bidWinner = $this->getPlayerByName($bidWinnerName);
        $situation = $this->play->getSituation($bidWinner);

        $distributionPlayerNames = array_filter(
            array_keys($situation['orderedPlayers']),
            fn($playerName) => $playerName !== $bidWinnerName
        );

        $distribution = ['distribution' => [
            array_pop($distributionPlayerNames) => $situation['orderedPlayers'][$bidWinnerName]['hand'][0],
            array_pop($distributionPlayerNames) => $situation['orderedPlayers'][$bidWinnerName]['hand'][0],
        ]];

        $this->play->handleMove(new GameMoveThousandStockDistribution(
            $bidWinner,
            $distribution,
            new GamePhaseThousandStockDistribution()
        ));
    }

    public function testThrowExceptionWhenHandleMoveStockDistributionToDealerFourPlayers(): void
    {
        $this->expectException(GamePlayThousandException::class);
        $this->expectExceptionMessage(GamePlayThousandException::MESSAGE_INCOMPATIBLE_MOVE);

        $this->play = $this->getGamePlay($this->getGameInvite(true));
        $this->updateGamePlayDeal([$this, 'getDealNoMarriage']);
        $this->processPhaseBidding();

        $bidWinnerName = $this->play->getSituation($this->players[0])['bidWinner'];
        $bidWinner = $this->getPlayerByName($bidWinnerName);
        $situation = $this->play->getSituation($bidWinner);

        $distributionPlayerNames = array_filter(
            array_keys($situation['orderedPlayers']),
            fn($playerName) => $playerName !== $bidWinnerName && $playerName !== $situation['dealer']
        );

        $distribution = ['distribution' => [
            array_pop($distributionPlayerNames) => $situation['orderedPlayers'][$bidWinnerName]['hand'][0],
            $situation['dealer'] => $situation['orderedPlayers'][$bidWinnerName]['hand'][1],
        ]];

        $this->play->handleMove(new GameMoveThousandStockDistribution(
            $bidWinner,
            $distribution,
            new GamePhaseThousandStockDistribution()
        ));
    }

    public function testThrowExceptionWhenHandleMoveStockDistributionCardsNotInHand(): void
    {
        $this->expectException(GamePlayThousandException::class);
        $this->expectExceptionMessage(GamePlayThousandException::MESSAGE_INCOMPATIBLE_MOVE);

        $this->updateGamePlayDeal([$this, 'getDealNoMarriage']);
        $this->processPhaseBidding();

        $bidWinnerName = $this->play->getSituation($this->players[0])['bidWinner'];
        $bidWinner = $this->getPlayerByName($bidWinnerName);
        $situation = $this->play->getSituation($bidWinner);

        $distributionPlayerNames = array_filter(
            array_keys($situation['orderedPlayers']),
            fn($playerName) => $playerName !== $bidWinnerName
        );

        $distributionPlayerOne = array_pop($distributionPlayerNames);
        $situationNotWinner = $this->play->getSituation($this->getPlayerByName($distributionPlayerOne));

        $distribution = ['distribution' => [
            $distributionPlayerOne => $situationNotWinner['orderedPlayers'][$distributionPlayerOne]['hand'][0],
            array_pop($distributionPlayerNames) => $situation['orderedPlayers'][$bidWinnerName]['hand'][1],
        ]];

        $this->play->handleMove(new GameMoveThousandStockDistribution(
            $bidWinner,
            $distribution,
            new GamePhaseThousandStockDistribution()
        ));
    }

    public function testGetSituationAfterStockDistributionMove(): void
    {
        $this->updateGamePlayDeal([$this, 'getDealNoMarriage']);
        $this->processPhaseBidding();

        $bidWinnerName = $this->play->getSituation($this->players[0])['bidWinner'];
        $bidWinner = $this->getPlayerByName($bidWinnerName);
        $situationSD = $this->play->getSituation($bidWinner);

        $distributionPlayerNames = array_filter(
            array_keys($situationSD['orderedPlayers']),
            fn($playerName) => $playerName !== $bidWinnerName
        );
        $distributionPlayerName1 = array_pop($distributionPlayerNames);
        $distributionPlayerName2 = array_pop($distributionPlayerNames);

        $distribution = ['distribution' => [
            $distributionPlayerName1 => $situationSD['orderedPlayers'][$bidWinnerName]['hand'][0],
            $distributionPlayerName2 => $situationSD['orderedPlayers'][$bidWinnerName]['hand'][1],
        ]];

        $this->play->handleMove(new GameMoveThousandStockDistribution(
            $bidWinner,
            $distribution,
            new GamePhaseThousandStockDistribution()
        ));

        $situation = $this->play->getSituation($bidWinner);

        $this->assertCount(8, $situation['orderedPlayers'][$bidWinnerName]['hand']);
        $this->assertEquals(8, $situation['orderedPlayers'][$distributionPlayerName1]['hand']);
        $this->assertEquals(8, $situation['orderedPlayers'][$distributionPlayerName2]['hand']);
        $this->assertCount(3, $situation['stockRecord']);
        $this->assertEquals($bidWinnerName, $situation['activePlayer']);
        $this->assertEquals((new GamePhaseThousandDeclaration())->getKey(), $situation['phase']['key']);
    }

    public function testThrowExceptionHandleMoveDeclarationNotBidWinner(): void
    {
        $this->expectException(GamePlayException::class);
        $this->expectExceptionMessage(GamePlayException::MESSAGE_NOT_CURRENT_PLAYER);

        $this->updateGamePlayDeal([$this, 'getDealNoMarriage']);
        $this->processPhaseBidding();
        $this->processPhaseStockDistribution();

        $situation = $this->play->getSituation($this->players[0]);
        $player = $situation['bidWinner'] === $this->players[0]->getName() ? $this->players[1] : $this->players[0];
        $this->play->handleMove(new GameMoveThousandDeclaration(
            $player,
            ['declaration' => 120],
            new GamePhaseThousandDeclaration()
        ));
    }

    public function testThrowExceptionWhenHandleMoveDeclarationLowerThanBid(): void
    {
        $this->expectException(GamePlayThousandException::class);
        $this->expectExceptionMessage(GamePlayThousandException::MESSAGE_RULE_WRONG_DECLARATION);

        $this->updateGamePlayDeal([$this, 'getDealNoMarriage']);
        $this->processPhaseBidding();
        $this->processPhaseStockDistribution();

        $situation = $this->play->getSituation($this->players[0]);
        $player = $this->getPlayerByName($situation['bidWinner']);

        $this->play->handleMove(new GameMoveThousandDeclaration(
            $player,
            ['declaration' => 100],
            new GamePhaseThousandDeclaration()
        ));
    }

    public function testThrowExceptionWhenHandleMoveDeclarationHigherThan300(): void
    {
        $this->expectException(GamePlayThousandException::class);
        $this->expectExceptionMessage(GamePlayThousandException::MESSAGE_RULE_WRONG_DECLARATION);

        $this->updateGamePlayDeal([$this, 'getDealNoMarriage']);
        $this->processPhaseBidding();
        $this->processPhaseStockDistribution();

        $situation = $this->play->getSituation($this->players[0]);
        $player = $this->getPlayerByName($situation['bidWinner']);

        $this->play->handleMove(new GameMoveThousandDeclaration(
            $player,
            ['declaration' => 310],
            new GamePhaseThousandDeclaration()
        ));
    }

    public function testThrowExceptionWhenHandleMoveDeclarationNot10PointsStep(): void
    {
        $this->expectException(GamePlayThousandException::class);
        $this->expectExceptionMessage(GamePlayThousandException::MESSAGE_RULE_WRONG_DECLARATION);

        $this->updateGamePlayDeal([$this, 'getDealNoMarriage']);
        $this->processPhaseBidding();
        $this->processPhaseStockDistribution();

        $situation = $this->play->getSituation($this->players[0]);
        $player = $this->getPlayerByName($situation['bidWinner']);

        $this->play->handleMove(new GameMoveThousandDeclaration(
            $player,
            ['declaration' => 125],
            new GamePhaseThousandDeclaration()
        ));
    }

    public function testGetSituationAfterDeclarationMove(): void
    {
        $this->updateGamePlayDeal([$this, 'getDealNoMarriage']);
        $this->processPhaseBidding();
        $this->processPhaseStockDistribution();

        $situationI0 = $this->play->getSituation($this->players[0]);
        $situationI1 = $this->play->getSituation($this->players[1]);
        $situationI2 = $this->play->getSituation($this->players[2]);

        $player = $this->getPlayerByName($situationI0['bidWinner']);

        $this->play->handleMove(new GameMoveThousandDeclaration(
            $player,
            ['declaration' => 130],
            new GamePhaseThousandDeclaration()
        ));

        $situationF0 = $this->play->getSituation($this->players[0]);
        $situationF1 = $this->play->getSituation($this->players[1]);
        $situationF2 = $this->play->getSituation($this->players[2]);
        $phaseKey = $situationF1['phase']['key'];
        $bidAmount = $situationF1['bidAmount'];
        unset(
            $situationI0['phase'], $situationI0['bidAmount'],
            $situationI1['phase'], $situationI1['bidAmount'],
            $situationI2['phase'], $situationI2['bidAmount'],
            $situationF0['phase'], $situationF0['bidAmount'],
            $situationF1['phase'], $situationF1['bidAmount'],
            $situationF2['phase'], $situationF2['bidAmount'],
        );

        $this->assertEquals((new GamePhaseThousandPlayFirstCard())->getKey(), $phaseKey);
        $this->assertEquals(130, $bidAmount);
        $this->assertEquals($situationI0, $situationF0);
        $this->assertEquals($situationI1, $situationF1);
        $this->assertEquals($situationI2, $situationF2);
    }

    public function testThrowExceptionWhenHandleMoveDeclarationBombAfterBidding(): void
    {
        $this->expectException(GamePlayThousandException::class);
        $this->expectExceptionMessage(GamePlayThousandException::MESSAGE_RULE_BOMB_ON_BID);

        $this->updateGamePlayDeal([$this, 'getDealNoMarriage']);
        $this->processPhaseBidding();
        $this->processPhaseStockDistribution();

        $situation = $this->play->getSituation($this->players[0]);
        $player = $this->getPlayerByName($situation['bidWinner']);

        $this->play->handleMove(new GameMoveThousandDeclaration(
            $player,
            ['declaration' => 0],
            new GamePhaseThousandDeclaration()
        ));
    }

    public function testThrowExceptionWhenHandleMoveDeclarationNoMoreBombs(): void
    {
        $this->expectException(GamePlayThousandException::class);
        $this->expectExceptionMessage(GamePlayThousandException::MESSAGE_RULE_BOMB_USED);

        $this->updateGamePlayDeal([$this, 'getDealNoMarriage']);
        $this->processPhaseBidding(false, 100);
        $this->processPhaseStockDistribution();

        $player = $this->play->getActivePlayer();

        $overwrite = $this->storageRepository->getOne($this->play->getId())->getGameData()['orderedPlayers'];
        $overwrite[$player->getName()]['bombRounds'] = [1];
        $this->updateGameData(['orderedPlayers' => $overwrite]);

        $this->play->handleMove(new GameMoveThousandDeclaration(
            $player,
            ['declaration' => 0],
            new GamePhaseThousandDeclaration()
        ));
    }

    public function testGetSituationAfterDeclarationBombMoveRound1(): void
    {
        $this->updateGamePlayDeal([$this, 'getDealNoMarriage']);
        $this->processPhaseBidding(false, 100);
        $this->processPhaseStockDistribution();

        $bidWinner = $this->play->getActivePlayer();
        $situationI0 = $this->play->getSituation($this->players[0]);
        $situationI1 = $this->play->getSituation($this->players[1]);
        $situationI2 = $this->play->getSituation($this->players[2]);

        $this->play->handleMove(new GameMoveThousandDeclaration(
            $bidWinner,
            ['declaration' => 0],
            new GamePhaseThousandDeclaration()
        ));

        $situationF0 = $this->play->getSituation($this->players[0]);
        $situationF1 = $this->play->getSituation($this->players[1]);
        $situationF2 = $this->play->getSituation($this->players[2]);

        $phase = $situationF0['phase']['key'];
        $points0 = $situationF0['orderedPlayers'][$this->players[0]->getName()]['points'];
        $points1 = $situationF1['orderedPlayers'][$this->players[1]->getName()]['points'];
        $points2 = $situationF2['orderedPlayers'][$this->players[2]->getName()]['points'];
        $ready0 = $situationF0['orderedPlayers'][$this->players[0]->getName()]['ready'];
        $ready1 = $situationF1['orderedPlayers'][$this->players[1]->getName()]['ready'];
        $ready2 = $situationF2['orderedPlayers'][$this->players[2]->getName()]['ready'];
        $bombRounds0 = $situationF0['orderedPlayers'][$this->players[0]->getName()]['bombRounds'];
        $bombRounds1 = $situationF1['orderedPlayers'][$this->players[1]->getName()]['bombRounds'];
        $bombRounds2 = $situationF2['orderedPlayers'][$this->players[2]->getName()]['bombRounds'];

        unset($situationI0['phase'], $situationI1['phase'], $situationI2['phase'], $situationF0['phase'], $situationF1['phase'], $situationF2['phase']);

        for ($i = 0; $i <= 2; $i++) {
            unset(
                $situationI0['orderedPlayers'][$this->players[$i]->getName()]['points'],
                $situationI0['orderedPlayers'][$this->players[$i]->getName()]['ready'],
                $situationI0['orderedPlayers'][$this->players[$i]->getName()]['bombRounds'],
                $situationI1['orderedPlayers'][$this->players[$i]->getName()]['points'],
                $situationI1['orderedPlayers'][$this->players[$i]->getName()]['ready'],
                $situationI1['orderedPlayers'][$this->players[$i]->getName()]['bombRounds'],
                $situationI2['orderedPlayers'][$this->players[$i]->getName()]['points'],
                $situationI2['orderedPlayers'][$this->players[$i]->getName()]['ready'],
                $situationI2['orderedPlayers'][$this->players[$i]->getName()]['bombRounds'],
                $situationF0['orderedPlayers'][$this->players[$i]->getName()]['points'],
                $situationF0['orderedPlayers'][$this->players[$i]->getName()]['ready'],
                $situationF0['orderedPlayers'][$this->players[$i]->getName()]['bombRounds'],
                $situationF1['orderedPlayers'][$this->players[$i]->getName()]['points'],
                $situationF1['orderedPlayers'][$this->players[$i]->getName()]['ready'],
                $situationF1['orderedPlayers'][$this->players[$i]->getName()]['bombRounds'],
                $situationF2['orderedPlayers'][$this->players[$i]->getName()]['points'],
                $situationF2['orderedPlayers'][$this->players[$i]->getName()]['ready'],
                $situationF2['orderedPlayers'][$this->players[$i]->getName()]['bombRounds'],
            );
        }

        $this->assertEquals((new GamePhaseThousandCountPoints())->getKey(), $phase);
        $this->assertEquals([1 => $this->players[0]->getId() === $bidWinner->getId() ? 0 : 60], $points0);
        $this->assertEquals([1 => $this->players[1]->getId() === $bidWinner->getId() ? 0 : 60], $points1);
        $this->assertEquals([1 => $this->players[2]->getId() === $bidWinner->getId() ? 0 : 60], $points2);
        $this->assertFalse($ready0);
        $this->assertFalse($ready1);
        $this->assertFalse($ready2);
        $this->assertEquals($this->players[0]->getId() === $bidWinner->getId() ? [1] : [], $bombRounds0);
        $this->assertEquals($this->players[1]->getId() === $bidWinner->getId() ? [1] : [], $bombRounds1);
        $this->assertEquals($this->players[2]->getId() === $bidWinner->getId() ? [1] : [], $bombRounds2);
        $this->assertEquals($situationI0, $situationF0);
        $this->assertEquals($situationI1, $situationF1);
        $this->assertEquals($situationI2, $situationF2);
    }

    public function testGetSituationAfterDeclarationBombMoveRound1FourPlayersNoStockPoints(): void
    {
        $this->play = $this->getGamePlay($this->getGameInvite(true));
        $this->updateGamePlayDeal([$this, 'getDealNoMarriage']);
        $this->processPhaseBidding(false, 100);
        $this->processPhaseStockDistribution(true);

        $bidWinner = $this->play->getActivePlayer();
        $situationI0 = $this->play->getSituation($this->players[0]);
        $situationI1 = $this->play->getSituation($this->players[1]);
        $situationI2 = $this->play->getSituation($this->players[2]);
        $situationI3 = $this->play->getSituation($this->players[3]);
        $dealer = $this->getPlayerByName($situationI0['dealer']);

        $this->play->handleMove(new GameMoveThousandDeclaration(
            $bidWinner,
            ['declaration' => 0],
            new GamePhaseThousandDeclaration()
        ));

        $situationF0 = $this->play->getSituation($this->players[0]);
        $situationF1 = $this->play->getSituation($this->players[1]);
        $situationF2 = $this->play->getSituation($this->players[2]);
        $situationF3 = $this->play->getSituation($this->players[3]);

        $phase = $situationF0['phase']['key'];
        $stockRecord = $situationF0['stockRecord'];
        $points0 = $situationF0['orderedPlayers'][$this->players[0]->getName()]['points'];
        $points1 = $situationF1['orderedPlayers'][$this->players[1]->getName()]['points'];
        $points2 = $situationF2['orderedPlayers'][$this->players[2]->getName()]['points'];
        $points3 = $situationF3['orderedPlayers'][$this->players[3]->getName()]['points'];
        $ready0 = $situationF0['orderedPlayers'][$this->players[0]->getName()]['ready'];
        $ready1 = $situationF1['orderedPlayers'][$this->players[1]->getName()]['ready'];
        $ready2 = $situationF2['orderedPlayers'][$this->players[2]->getName()]['ready'];
        $ready3 = $situationF3['orderedPlayers'][$this->players[3]->getName()]['ready'];
        $bombRounds0 = $situationF0['orderedPlayers'][$this->players[0]->getName()]['bombRounds'];
        $bombRounds1 = $situationF1['orderedPlayers'][$this->players[1]->getName()]['bombRounds'];
        $bombRounds2 = $situationF2['orderedPlayers'][$this->players[2]->getName()]['bombRounds'];
        $bombRounds3 = $situationF3['orderedPlayers'][$this->players[3]->getName()]['bombRounds'];

        unset(
            $situationI0['phase'], $situationI0['stockRecord'],
            $situationI1['phase'], $situationI1['stockRecord'],
            $situationI2['phase'], $situationI2['stockRecord'],
            $situationI3['phase'], $situationI3['stockRecord'],
            $situationF0['phase'], $situationF0['stockRecord'],
            $situationF1['phase'], $situationF1['stockRecord'],
            $situationF2['phase'], $situationF2['stockRecord'],
            $situationF3['phase'], $situationF3['stockRecord'],
        );

        for ($i = 0; $i <= 3; $i++) {
            unset(
                $situationI0['orderedPlayers'][$this->players[$i]->getName()]['points'],
                $situationI0['orderedPlayers'][$this->players[$i]->getName()]['ready'],
                $situationI0['orderedPlayers'][$this->players[$i]->getName()]['bombRounds'],
                $situationI1['orderedPlayers'][$this->players[$i]->getName()]['points'],
                $situationI1['orderedPlayers'][$this->players[$i]->getName()]['ready'],
                $situationI1['orderedPlayers'][$this->players[$i]->getName()]['bombRounds'],
                $situationI2['orderedPlayers'][$this->players[$i]->getName()]['points'],
                $situationI2['orderedPlayers'][$this->players[$i]->getName()]['ready'],
                $situationI2['orderedPlayers'][$this->players[$i]->getName()]['bombRounds'],
                $situationI3['orderedPlayers'][$this->players[$i]->getName()]['points'],
                $situationI3['orderedPlayers'][$this->players[$i]->getName()]['ready'],
                $situationI3['orderedPlayers'][$this->players[$i]->getName()]['bombRounds'],
                $situationF0['orderedPlayers'][$this->players[$i]->getName()]['points'],
                $situationF0['orderedPlayers'][$this->players[$i]->getName()]['ready'],
                $situationF0['orderedPlayers'][$this->players[$i]->getName()]['bombRounds'],
                $situationF1['orderedPlayers'][$this->players[$i]->getName()]['points'],
                $situationF1['orderedPlayers'][$this->players[$i]->getName()]['ready'],
                $situationF1['orderedPlayers'][$this->players[$i]->getName()]['bombRounds'],
                $situationF2['orderedPlayers'][$this->players[$i]->getName()]['points'],
                $situationF2['orderedPlayers'][$this->players[$i]->getName()]['ready'],
                $situationF2['orderedPlayers'][$this->players[$i]->getName()]['bombRounds'],
                $situationF3['orderedPlayers'][$this->players[$i]->getName()]['points'],
                $situationF3['orderedPlayers'][$this->players[$i]->getName()]['ready'],
                $situationF3['orderedPlayers'][$this->players[$i]->getName()]['bombRounds'],
            );
        }

        $expectedPoints0 = in_array($this->players[0]->getId(), [$bidWinner->getId(), $dealer->getId()]) ? 0 : 60;
        $expectedPoints1 = in_array($this->players[1]->getId(), [$bidWinner->getId(), $dealer->getId()]) ? 0 : 60;
        $expectedPoints2 = in_array($this->players[2]->getId(), [$bidWinner->getId(), $dealer->getId()]) ? 0 : 60;
        $expectedPoints3 = in_array($this->players[3]->getId(), [$bidWinner->getId(), $dealer->getId()]) ? 0 : 60;

        $this->assertEquals((new GamePhaseThousandCountPoints())->getKey(), $phase);
        $this->assertEquals([1 => $expectedPoints0], $points0);
        $this->assertEquals([1 => $expectedPoints1], $points1);
        $this->assertEquals([1 => $expectedPoints2], $points2);
        $this->assertEquals([1 => $expectedPoints3], $points3);
        $this->assertFalse($ready0);
        $this->assertFalse($ready1);
        $this->assertFalse($ready2);
        $this->assertFalse($ready3);
        $this->assertEquals($this->players[0]->getId() === $bidWinner->getId() ? [1] : [], $bombRounds0);
        $this->assertEquals($this->players[1]->getId() === $bidWinner->getId() ? [1] : [], $bombRounds1);
        $this->assertEquals($this->players[2]->getId() === $bidWinner->getId() ? [1] : [], $bombRounds2);
        $this->assertEquals($this->players[3]->getId() === $bidWinner->getId() ? [1] : [], $bombRounds3);
        $this->assertEquals($situationI0, $situationF0);
        $this->assertEquals($situationI1, $situationF1);
        $this->assertEquals($situationI2, $situationF2);
        $this->assertEquals($situationI3, $situationF3);
        $this->assertCount(3, $stockRecord);
    }

    public function testGetSituationAfterDeclarationBombMoveNotBidWinnerOnBarrelDontGetPoints(): void
    {
        $this->updateGamePlayDeal([$this, 'getDealNoMarriage']);
        $this->processPhaseBidding(false, 100);
        $this->processPhaseStockDistribution();

        $player = $this->play->getActivePlayer();

        $overwrite = $this->storageRepository->getOne($this->play->getId())->getGameData();
        $preparedPoints = [
            [1 => 0, 2 => 0, 3 => 0, 4 => 0],
            [1 => 200, 2 => 400, 3 => 600, 4 => 750],
            [1 => 200, 2 => 410, 3 => 610, 4 => 820],
        ];
        for ($i = 0; $i <= 2; $i++) {
            $overwrite['orderedPlayers'][$this->players[$i]->getName()]['points'] =
                $this->players[$i]->getID() === $player->getId()
                    ? array_shift($preparedPoints)
                    : array_pop($preparedPoints);
            $overwrite['orderedPlayers'][$this->players[$i]->getName()]['barrel'] =
                $overwrite['orderedPlayers'][$this->players[$i]->getName()]['points'][4] >= 800;
        }
        $overwrite['round'] = 5;
        $this->updateGameData(['orderedPlayers' => $overwrite['orderedPlayers'], 'round' => $overwrite['round']]);

        $this->play->handleMove(new GameMoveThousandDeclaration(
            $player,
            ['declaration' => 0],
            new GamePhaseThousandDeclaration()
        ));

        $situation = $this->play->getSituation($player);

        $points4R0 = $situation['orderedPlayers'][$this->players[0]->getName()]['points'][4];
        $points4R1 = $situation['orderedPlayers'][$this->players[1]->getName()]['points'][4];
        $points4R2 = $situation['orderedPlayers'][$this->players[2]->getName()]['points'][4];
        $points5R0 = $situation['orderedPlayers'][$this->players[0]->getName()]['points'][5];
        $points5R1 = $situation['orderedPlayers'][$this->players[1]->getName()]['points'][5];
        $points5R2 = $situation['orderedPlayers'][$this->players[2]->getName()]['points'][5];
        $barrel5R0 = $situation['orderedPlayers'][$this->players[0]->getName()]['barrel'];
        $barrel5R1 = $situation['orderedPlayers'][$this->players[1]->getName()]['barrel'];
        $barrel5R2 = $situation['orderedPlayers'][$this->players[2]->getName()]['barrel'];

        $expectedPoints5R0 = $this->players[0]->getId() === $player->getId() ? $points4R0 : ($points4R0 === 820 ? 820 : $points4R0 + 60);
        $expectedPoints5R1 = $this->players[1]->getId() === $player->getId() ? $points4R1 : ($points4R1 === 820 ? 820 : $points4R1 + 60);
        $expectedPoints5R2 = $this->players[2]->getId() === $player->getId() ? $points4R2 : ($points4R2 === 820 ? 820 : $points4R2 + 60);

        $expectedBarrel5R0 = $expectedPoints5R0 >= 800;
        $expectedBarrel5R1 = $expectedPoints5R1 >= 800;
        $expectedBarrel5R2 = $expectedPoints5R2 >= 800;

        $this->assertEquals($expectedPoints5R0, $points5R0);
        $this->assertEquals($expectedPoints5R1, $points5R1);
        $this->assertEquals($expectedPoints5R2, $points5R2);
        $this->assertEquals($expectedBarrel5R0, $barrel5R0);
        $this->assertEquals($expectedBarrel5R1, $barrel5R1);
        $this->assertEquals($expectedBarrel5R2, $barrel5R2);
    }

    public function testGetSituationAfterDeclarationBombMoveFourPlayersAcesInStock(): void
    {
        $this->play = $this->getGamePlay($this->getGameInvite(true));
        $this->updateGamePlayDeal([$this, 'getDealTwoAcesInStock']);
        $this->processPhaseBidding(false, 100);
        $this->processPhaseStockDistribution(true);

        $player = $this->play->getActivePlayer();

        $this->play->handleMove(new GameMoveThousandDeclaration(
            $player,
            ['declaration' => 0],
            new GamePhaseThousandDeclaration()
        ));

        $situation = $this->play->getSituation($player);
        $dealerName = $situation['dealer'];

        $this->assertEquals(100, $situation['orderedPlayers'][$dealerName]['points'][1]);
        $this->assertCount(3, $situation['stockRecord']);
    }

    public function testGetSituationAfterDeclarationBombMoveFourPlayersMarriageInStock(): void
    {
        $this->play = $this->getGamePlay($this->getGameInvite(true));
        $this->updateGamePlayDeal([$this, 'getDealMarriageInStock']);
        $this->processPhaseBidding(false, 100);
        $this->processPhaseStockDistribution(true);

        $player = $this->play->getActivePlayer();
        $dealerName = $this->play->getSituation($player)['dealer'];

        $this->play->handleMove(new GameMoveThousandDeclaration(
            $player,
            ['declaration' => 0],
            new GamePhaseThousandDeclaration()
        ));

        $situation = $this->play->getSituation($player);

        $this->assertEquals(40, $situation['orderedPlayers'][$dealerName]['points'][1]);
        $this->assertCount(3, $situation['stockRecord']);
    }

    public function testGetSituationAfterDeclarationBombMoveFourPlayersMarriageInStockDealerOnBarrel(): void
    {
        $this->play = $this->getGamePlay($this->getGameInvite(true));
        $this->updateGamePlayDeal([$this, 'getDealMarriageInStock']);
        $this->processPhaseBidding(false, 100);
        $this->processPhaseStockDistribution(true);

        $player = $this->play->getActivePlayer();
        $dealerName = $this->play->getSituation($player)['dealer'];

        $overwrite = $this->storageRepository->getOne($this->play->getId())->getGameData()['orderedPlayers'];
        $overwrite[$dealerName]['barrel'] = true;
        $this->updateGameData(['orderedPlayers' => $overwrite]);

        $this->play->handleMove(new GameMoveThousandDeclaration(
            $player,
            ['declaration' => 0],
            new GamePhaseThousandDeclaration()
        ));

        $situation = $this->play->getSituation($player);

        $this->assertEquals(0, $situation['orderedPlayers'][$dealerName]['points'][1]);
        $this->assertCount(3, $situation['stockRecord']);
    }




    // count  points  phase  should  be  only   to   confirm  readiness  for  next  phase
    // counting points itself should happen after accepted bomb (Declaration) or last ThirdCard (last in hand)

    // later... test barrel not update if option value for barrel is set to 0
}
