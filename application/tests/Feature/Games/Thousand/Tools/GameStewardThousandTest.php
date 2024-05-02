<?php

namespace Games\Thousand\Tools;

use App\GameCore\GameElements\GameDeck\PlayingCard\CollectionPlayingCardUnique;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCard;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDealer;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDeckProvider;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardRank;
use App\GameCore\GameElements\GamePlayPlayers\CollectionGamePlayPlayers;
use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameOption\GameOption;
use App\GameCore\GameOptionValue\GameOptionValue;
use App\GameCore\GameSetup\GameSetup;
use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\Collection;
use App\Games\Thousand\Elements\GamePhaseThousandPlayFirstCard;
use App\Games\Thousand\Elements\GamePhaseThousandPlaySecondCard;
use App\Games\Thousand\Elements\GamePhaseThousandPlayThirdCard;
use App\Games\Thousand\Tools\GameStewardThousand;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class GameStewardThousandTest extends TestCase
{
    private GameStewardThousand $steward;
    private PlayingCardDeckProvider $deckProvider;
    private PlayingCardDealer $dealer;
    private Collection $handler;

    public function setUp(): void
    {
        parent::setUp();
        $this->deckProvider = App::make(PlayingCardDeckProvider::class);
        $this->dealer = App::make(PlayingCardDealer::class);
        $this->handler = App::make(Collection::class);

        $this->steward = $this->getSteward();
    }

    private function getPlayersCollectionMock(bool $fourPlayers = false): CollectionGamePlayPlayers
    {
        $players = [];
        for ($i = 0; $i <= 2 + ($fourPlayers ? 1 : 0); $i++) {
            $player = $this->createMock(Player::class);
            $player->method('getId')->willReturn("Id-$i");
            $player->method('getName')->willReturn("Player Name $i");
            $players[] = $player;
        }

        return new CollectionGamePlayPlayers(App::make(Collection::class), $players);
    }

    private function getGameInviteMock(bool $fourPlayers = false, mixed $bombs = 1, mixed $barrel = null): GameInvite
    {
        $invite = $this->createMock(GameInvite::class);
        $setup = $this->createMock(GameSetup::class);
        $option = $this->createMock(GameOption::class);
        $configuredValue = $this->createMock(GameOptionValue::class);

        if (isset($bombs)) {

            $configuredValue->method('getValue')->willReturn($bombs);
            $option->method('getConfiguredValue')->willReturn($configuredValue);
            $setup->method('getOption')->with('thousand-number-of-bombs')->willReturn($option);

        } elseif (isset($barrel)) {

            $configuredValue->method('getValue')->willReturn($barrel);
            $option->method('getConfiguredValue')->willReturn($configuredValue);
            $setup->method('getOption')->with('thousand-barrel-points')->willReturn($option);
        }

        $invite->method('getGameSetup')->willReturn($setup);

        return $invite;
    }

    private function getSteward(bool $fourPlayers = false, mixed $bombs = 1, mixed $barrel = null): GameStewardThousand
    {
        return new GameStewardThousand(
            $this->getPlayersCollectionMock($fourPlayers),
            $this->getGameInviteMock($fourPlayers, $bombs, $barrel),
            $this->dealer
        );
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(GameStewardThousand::class, $this->steward);
    }

    public function testGetCardPoints(): void
    {
        $rankMock = $this->createMock(PlayingCardRank::class);
        $rankMock->method('getKey')->willReturn('A');
        $cardMock = $this->createMock(PlayingCard::class);
        $cardMock->method('getRank')->willReturn($rankMock);

        $this->assertEquals(11, $this->steward->getCardPoints($cardMock));
    }

    public function testIsFirstCardPhase(): void
    {
        $phaseFirst = new GamePhaseThousandPlayFirstCard();
        $phaseSecond = new GamePhaseThousandPlaySecondCard();
        $phaseThird = new GamePhaseThousandPlayThirdCard();

        $this->assertTrue($this->steward->isFirstCardPhase($phaseFirst));
        $this->assertFalse($this->steward->isFirstCardPhase($phaseSecond));
        $this->assertFalse($this->steward->isFirstCardPhase($phaseThird));
    }

    public function testIsSecondCardPhase(): void
    {
        $phaseFirst = new GamePhaseThousandPlayFirstCard();
        $phaseSecond = new GamePhaseThousandPlaySecondCard();
        $phaseThird = new GamePhaseThousandPlayThirdCard();

        $this->assertFalse($this->steward->isSecondCardPhase($phaseFirst));
        $this->assertTrue($this->steward->isSecondCardPhase($phaseSecond));
        $this->assertFalse($this->steward->isSecondCardPhase($phaseThird));
    }

    public function testIsThirdCardPhase(): void
    {
        $phaseFirst = new GamePhaseThousandPlayFirstCard();
        $phaseSecond = new GamePhaseThousandPlaySecondCard();
        $phaseThird = new GamePhaseThousandPlayThirdCard();

        $this->assertFalse($this->steward->isThirdCardPhase($phaseFirst));
        $this->assertFalse($this->steward->isThirdCardPhase($phaseSecond));
        $this->assertTrue($this->steward->isThirdCardPhase($phaseThird));
    }

    public function testIsFourPlayersDealer(): void
    {
        $player = $this->createMock(Player::class);
        $player->method('getId')->willReturn('player');
        $dealer = $this->createMock(Player::class);
        $dealer->method('getId')->willReturn('dealer');

        $this->assertFalse($this->steward->isFourPlayersDealer($player, $dealer));
        $this->assertFalse($this->steward->isFourPlayersDealer($player, $player));
        $this->assertFalse($this->getSteward(true)->isFourPlayersDealer($player, $dealer));
        $this->assertTrue($this->getSteward(true)->isFourPlayersDealer($player, $player));
    }

    public function testCountStockMarriagePoints(): void
    {
        $deck = $this->deckProvider->getDeckSchnapsen();

        $cards100 = [$deck->getOne('K-H'), $deck->getOne('Q-H'), $deck->getOne('A-H')];
        $deck100 = new CollectionPlayingCardUnique(clone $this->handler, $cards100);

        $cards000 = [$deck->getOne('K-H'), $deck->getOne('Q-C'), $deck->getOne('A-D')];
        $deck000 = new CollectionPlayingCardUnique(clone $this->handler, $cards000);

        $cards080 = [$deck->getOne('K-D'), $deck->getOne('Q-D'), $deck->getOne('K-H')];
        $deck080 = new CollectionPlayingCardUnique(clone $this->handler, $cards080);

        $this->assertEquals(100, $this->steward->countStockMarriagePoints($deck100));
        $this->assertEquals(0, $this->steward->countStockMarriagePoints($deck000));
        $this->assertEquals(80, $this->steward->countStockMarriagePoints($deck080));
    }

    public function testIsLastBiddingMove(): void
    {
        $DataOnePass = ['123' => ['bid' => 'pass'], '234' => ['bid' => 110], '345' => ['bid' => 120]];
        $DataTwoPass = ['123' => ['bid' => 'pass'], '234' => ['bid' => 110], '345' => ['bid' => 'pass']];

        $this->assertFalse($this->steward->isLastBiddingMove(150, $DataOnePass));
        $this->assertTrue($this->steward->isLastBiddingMove(150, $DataTwoPass));
        $this->assertTrue($this->steward->isLastBiddingMove(300, $DataOnePass));
        $this->assertTrue($this->steward->isLastBiddingMove(300, $DataTwoPass));
    }

    public function testGetHighestBiddingPlayer(): void
    {
        $bids = ['Id-0' => ['bid' => 110], 'Id-1' => ['bid' => 120], 'Id-2' => ['bid' => 'pass']];
        $bidWinner = $this->steward->getHighestBiddingPlayer($bids);

        $this->assertEquals('Id-1', $bidWinner->getId());
    }

    public function testGetNextOrderedPlayer(): void
    {
        $data3P = ['Id-0' => ['seat' => 3, 'bid' => 'pass'], 'Id-1' => ['seat' => 2, 'bid' => null], 'Id-2' => ['seat' => 1, 'bid' => null]];
        $data4P = ['Id-0' => ['seat' => 1, 'bid' => 'pass'], 'Id-1' => ['seat' => 2, 'bid' => null], 'Id-2' => ['seat' => 3, 'bid' => null], 'Id-3' => ['seat' => 4, 'bid' => null]];

        $steward3P = $this->steward;
        $steward4P = $this->getSteward(true);

        $players = $this->getPlayersCollectionMock(true);
        $player0 = $players->getOne('Id-0');
        $player1 = $players->getOne('Id-1');
        $player2 = $players->getOne('Id-2');
        $player3 = $players->getOne('Id-3');

        $this->assertEquals($player2->getId(), $steward3P->getNextOrderedPlayer($player0, $player2, $data3P)->getId());
        $this->assertEquals($player1->getId(), $steward3P->getNextOrderedPlayer($player2, $player2, $data3P)->getId());
        $this->assertEquals($player2->getId(), $steward3P->getNextOrderedPlayer($player1, $player2, $data3P)->getId());
        $this->assertEquals($player1->getId(), $steward4P->getNextOrderedPlayer($player0, $player2, $data4P)->getId());
        $this->assertEquals($player3->getId(), $steward4P->getNextOrderedPlayer($player1, $player2, $data4P)->getId());
        $this->assertEquals($player3->getId(), $steward4P->getNextOrderedPlayer($player2, $player2, $data4P)->getId());
        $this->assertEquals($player1->getId(), $steward4P->getNextOrderedPlayer($player3, $player2, $data4P)->getId());
    }

    public function testShuffleAndDealCards(): void
    {
        $deck3P = $this->deckProvider->getDeckSchnapsen();
        $deck4P = $this->deckProvider->getDeckSchnapsen();
        $stock3P = $this->dealer->getEmptyStock();
        $stock4P = $this->dealer->getEmptyStock();
        $data3P = [
            'Id-0' => ['hand' => $this->dealer->getEmptyStock(), 'seat' => 1, 'bid' => null],
            'Id-1' => ['hand' => $this->dealer->getEmptyStock(), 'seat' => 2, 'bid' => null],
            'Id-2' => ['hand' => $this->dealer->getEmptyStock(), 'seat' => 3, 'bid' => null],
        ];
        $data4P = [
            'Id-0' => ['hand' => $this->dealer->getEmptyStock(), 'seat' => 1, 'bid' => null],
            'Id-1' => ['hand' => $this->dealer->getEmptyStock(), 'seat' => 2, 'bid' => null],
            'Id-2' => ['hand' => $this->dealer->getEmptyStock(), 'seat' => 3, 'bid' => null],
            'Id-3' => ['hand' => $this->dealer->getEmptyStock(), 'seat' => 4, 'bid' => null],
        ];

        $dealer = $this->getPlayersCollectionMock()->getOne('Id-0');

        $steward3P = $this->steward;
        $steward4P = $this->getSteward(true);

        $steward3P->shuffleAndDealCards($deck3P, $stock3P, $dealer, $data3P);
        $steward4P->shuffleAndDealCards($deck4P, $stock4P, $dealer, $data4P);

        $this->assertEquals(0, $deck3P->count());
        $this->assertEquals(0, $deck4P->count());
        $this->assertEquals(3, $stock3P->count());
        $this->assertEquals(3, $stock4P->count());
        $this->assertEquals(7, $data3P['Id-0']['hand']->count());
        $this->assertEquals(7, $data3P['Id-1']['hand']->count());
        $this->assertEquals(7, $data3P['Id-2']['hand']->count());
        $this->assertEquals(0, $data4P['Id-0']['hand']->count());
        $this->assertEquals(7, $data4P['Id-1']['hand']->count());
        $this->assertEquals(7, $data4P['Id-2']['hand']->count());
        $this->assertEquals(7, $data4P['Id-3']['hand']->count());
    }

    public function testGetTrickWinner(): void
    {
        $data3P = [
            'Id-0' => ['hand' => $this->dealer->getEmptyStock(), 'seat' => 1, 'bid' => null],
            'Id-1' => ['hand' => $this->dealer->getEmptyStock(), 'seat' => 2, 'bid' => null],
            'Id-2' => ['hand' => $this->dealer->getEmptyStock(), 'seat' => 3, 'bid' => null],
        ];

        $deck = $this->deckProvider->getDeckSchnapsen();

        $dealer = $this->getPlayersCollectionMock()->getOne('Id-0');
        $turnLead = $dealer;
        $trumpSuit = $deck->getOne('A-H')->getSuit();
        $turnSuit = $deck->getOne('A-D')->getSuit();

        $cardsTrumpBeatsNotTrump = [$deck->getOne('10-D'), $deck->getOne('A-D'), $deck->getOne('9-H')];
        $cardsTrumpBeatsTrumpRank = [$deck->getOne('10-D'), $deck->getOne('10-H'), $deck->getOne('A-H')];
        $cardsTurnBeatsTurnRank = [$deck->getOne('10-D'), $deck->getOne('K-C'), $deck->getOne('A-D')];
        $tableTrumpBeatsNotTrump = $this->dealer->getEmptyStock()->reset($cardsTrumpBeatsNotTrump);
        $tableTrumpBeatsTrumpRank = $this->dealer->getEmptyStock()->reset($cardsTrumpBeatsTrumpRank);
        $tableTurnBeatsTurnRank = $this->dealer->getEmptyStock()->reset($cardsTurnBeatsTurnRank);

        $this->assertEquals('Id-2', $this->steward->getTrickWinner(
            $dealer, $turnLead, $trumpSuit, $turnSuit, $tableTrumpBeatsNotTrump, $data3P
        )->getId());
        $this->assertEquals('Id-2', $this->steward->getTrickWinner(
            $dealer, $turnLead, $trumpSuit, $turnSuit, $tableTrumpBeatsTrumpRank, $data3P
        )->getId());
        $this->assertEquals('Id-2', $this->steward->getTrickWinner(
            $dealer, $turnLead, $trumpSuit, $turnSuit, $tableTurnBeatsTurnRank, $data3P
        )->getId());
    }

    public function testHasPlayerUsedMaxBombMoves(): void
    {
        $data3P = [
            'Id-0' => ['hand' => $this->dealer->getEmptyStock(), 'seat' => 1, 'bid' => null, 'bombRounds' => [1, 4]],
            'Id-1' => ['hand' => $this->dealer->getEmptyStock(), 'seat' => 2, 'bid' => null, 'bombRounds' => [2]],
            'Id-2' => ['hand' => $this->dealer->getEmptyStock(), 'seat' => 3, 'bid' => null, 'bombRounds' => []],
        ];

        $players = $this->getPlayersCollectionMock();
        $this->steward = $this->getSteward(false, 2, null);

        $this->assertTrue($this->steward->hasPlayerUsedMaxBombMoves($players->getOne('Id-0'), $data3P));
        $this->assertFalse($this->steward->hasPlayerUsedMaxBombMoves($players->getOne('Id-1'), $data3P));
        $this->assertFalse($this->steward->hasPlayerUsedMaxBombMoves($players->getOne('Id-2'), $data3P));
    }

    public function testIsPlayerEligibleForPoints(): void
    {
        $data3P = [
            'Id-0' => ['barrel' => false],
            'Id-1' => ['barrel' => true],
            'Id-2' => ['barrel' => true],
        ];

        $players = $this->getPlayersCollectionMock();
        $bidWinner = $players->getOne('Id-1');

        $this->assertTrue($this->steward->isPlayerEligibleForPoints($players->getOne('Id-0'), $bidWinner, $data3P));
        $this->assertTrue($this->steward->isPlayerEligibleForPoints($players->getOne('Id-1'), $bidWinner, $data3P));
        $this->assertFalse($this->steward->isPlayerEligibleForPoints($players->getOne('Id-2'), $bidWinner, $data3P));
    }

    public function testHasMarriageAtHand(): void
    {
        $deck = $this->deckProvider->getDeckSchnapsen();
        $data3P = [
            'Id-0' => ['hand' => $this->dealer->getCardsByKeys($deck, ['A-H', '10-H', 'K-H', 'Q-H', 'J-H', '9-H', 'Q-S'], true, true)],
            'Id-1' => ['hand' => $this->dealer->getCardsByKeys($deck, ['A-D', '10-D', 'K-D', 'Q-C', 'J-D', '9-D', '10-S'], true, true)],
            'Id-2' => ['hand' => $this->dealer->getCardsByKeys($deck, ['A-C', '10-C', 'J-S', 'A-S', 'J-C', '9-C', '9-S'], true, true)],
        ];

        $players = $this->getPlayersCollectionMock();

        $this->assertTrue($this->steward->hasMarriageAtHand($players->getOne('Id-0'), $data3P));
        $this->assertFalse($this->steward->hasMarriageAtHand($players->getOne('Id-1'), $data3P));
        $this->assertFalse($this->steward->hasMarriageAtHand($players->getOne('Id-2'), $data3P));
    }

    public function testCountPlayedPoints(): void
    {
        $deck = $this->deckProvider->getDeckSchnapsen();
        $tricks0 = $this->dealer->getCardsByKeys($deck, ['A-H', '10-H', 'K-H', 'Q-H', 'J-H', '9-H', 'Q-S'], true, true);
        $tricks1 = $this->dealer->getCardsByKeys($deck, ['A-D', '10-D', 'K-D', 'Q-C', 'J-D', '9-D', '10-S'], true, true);
        $tricks2 = $this->dealer->getCardsByKeys($deck, ['A-C', '10-C', 'J-S', 'A-S', 'J-C', '9-C', '9-S'], true, true);

        $data3P = [
            'Id-0' => ['tricks' => $tricks0, 'trumps' => ['K-H']],
            'Id-1' => ['tricks' => $tricks1, 'trumps' => []],
            'Id-2' => ['tricks' => $tricks2, 'trumps' => []],
        ];

        $players = $this->getPlayersCollectionMock();
        $player0 = $players->getOne('Id-0');
        $player1 = $players->getOne('Id-1');
        $player2 = $players->getOne('Id-2');

        $this->assertEquals(100, $this->steward->countPlayedPoints($player0, $player0, 100, $data3P));
        $this->assertEquals(-120, $this->steward->countPlayedPoints($player1, $player1, 120, $data3P));
        $this->assertEquals(130, $this->steward->countPlayedPoints($player0, $player1, 100, $data3P));
        $this->assertEquals(40, $this->steward->countPlayedPoints($player2, $player0, 100, $data3P));
    }

    public function testSetBarrelStatus(): void
    {
        $data3P = [
            'Id-0' => ['points' => [1 => 790], 'barrel' => false],
            'Id-1' => ['points' => [1 => 810], 'barrel' => false],
            'Id-2' => ['points' => [1 => 900], 'barrel' => false],
        ];

        $players = $this->getPlayersCollectionMock();
        $this->getSteward(false, null, 800)->setBarrelStatus($players->getOne('Id-0'), 1, $data3P);
        $this->getSteward(false, null, 800)->setBarrelStatus($players->getOne('Id-1'), 1, $data3P);
        $this->getSteward(false, null, 0)->setBarrelStatus($players->getOne('Id-2'), 1, $data3P);

        $this->assertFalse($data3P['Id-0']['barrel']);
        $this->assertTrue($data3P['Id-1']['barrel']);
        $this->assertFalse($data3P['Id-2']['barrel']);
    }

    public function testCountStockAcesPoints(): void
    {
        $deck = $this->deckProvider->getDeckSchnapsen();
        $stock000 = $this->dealer->getCardsByKeys($deck, ['J-H', '9-H', 'Q-S'], true, true);
        $stock050 = $this->dealer->getCardsByKeys($deck, ['A-D', 'J-D', 'K-D'], true, true);
        $stock100 = $this->dealer->getCardsByKeys($deck, ['A-C', 'J-S', 'A-S'], true, true);
        $stock150 = $this->dealer->getCardsByKeys($deck, ['A-C', 'A-S', 'A-H'], true, true);

        $this->assertEquals(0, $this->steward->countStockAcesPoints($stock000));
        $this->assertEquals(50, $this->steward->countStockAcesPoints($stock050));
        $this->assertEquals(100, $this->steward->countStockAcesPoints($stock100));
        $this->assertEquals(150, $this->steward->countStockAcesPoints($stock150));
    }

}
