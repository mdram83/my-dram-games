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
use App\Games\Thousand\Tools\CollectionPlayerDataThousand;
use App\Games\Thousand\Tools\GameDataThousand;
use App\Games\Thousand\Tools\GameStewardThousand;
use App\Games\Thousand\Tools\PlayerDataThousand;
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

    private function getCollectionPlayerDataThousand(bool $fourPlayers = false): CollectionPlayerDataThousand
    {
        $players = $this->getPlayersCollectionMock($fourPlayers);
        $collection = new CollectionPlayerDataThousand(clone $this->handler);

        for ($i = 0; $i <= 2 + ($fourPlayers ? 1 : 0); $i++) {
            $collection->add(new PlayerDataThousand($players->getOne("Id-$i")));
        }

        return $collection;
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

    public function testIsLastBiddingMove(): void
    {
        $dataBid = $this->getCollectionPlayerDataThousand();
        $dataPass = $this->getCollectionPlayerDataThousand();

        $dataBid->each(function($playerData, $playerId) {
            $playerData->bid = in_array($playerData->getId(), ['Id-0']) ? 'pass' : 110;
            return $playerData;
        });

        $dataPass->each(function($playerData, $playerId) {
            $playerData->bid = in_array($playerData->getId(), ['Id-0', 'Id-1']) ? 'pass' : 110;
            return $playerData;
        });

        $this->assertFalse($this->steward->isLastBiddingMove(150, $dataBid));
        $this->assertTrue($this->steward->isLastBiddingMove(150, $dataPass));
        $this->assertTrue($this->steward->isLastBiddingMove(300, $dataBid));
        $this->assertTrue($this->steward->isLastBiddingMove(300, $dataPass));
    }

    public function testGetHighestBiddingPlayer(): void
    {
        $data = $this->getCollectionPlayerDataThousand();
        $data->each(function($playerData, $playerId) {
            $bids = ['Id-0' => 110, 'Id-1' => 120, 'Id-2' => 'pass'];
            $playerData->bid = $bids[$playerId];
            return $playerData;
        });
        $bidWinner = $this->steward->getHighestBiddingPlayer($data);

        $this->assertEquals('Id-1', $bidWinner->getId());
    }

    public function testGetNextOrderedPlayer(): void
    {
        $data3P = $this->getCollectionPlayerDataThousand();
        $data3P->each(function($playerData, $playerId) {
            $params = ['Id-0' => ['seat' => 3, 'bid' => 'pass'], 'Id-1' => ['seat' => 2, 'bid' => null], 'Id-2' => ['seat' => 1, 'bid' => null]];
            $playerData->bid = $params[$playerId]['bid'];
            $playerData->seat = $params[$playerId]['seat'];
            return $playerData;
        });

        $data4P = $this->getCollectionPlayerDataThousand(true);
        $data4P->each(function($playerData, $playerId) {
            $params = ['Id-0' => ['seat' => 1, 'bid' => 'pass'], 'Id-1' => ['seat' => 2, 'bid' => null], 'Id-2' => ['seat' => 3, 'bid' => null], 'Id-3' => ['seat' => 4, 'bid' => null]];
            $playerData->bid = $params[$playerId]['bid'];
            $playerData->seat = $params[$playerId]['seat'];
            return $playerData;
        });

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
        $gData3P = new GameDataThousand();
        $gData3P->deck = $this->deckProvider->getDeckSchnapsen();
        $gData3P->stock = $this->dealer->getEmptyStock();
        $gData3P->dealer = $this->getPlayersCollectionMock()->getOne('Id-0');

        $gData4P = new GameDataThousand();
        $gData4P->deck = $this->deckProvider->getDeckSchnapsen();
        $gData4P->stock = $this->dealer->getEmptyStock();
        $gData4P->dealer = $this->getPlayersCollectionMock()->getOne('Id-0');

        $data3P = $this->getCollectionPlayerDataThousand();
        $data3P->each(function($playerData, $playerId) {
            $params = ['Id-0' => 1, 'Id-1' => 2, 'Id-2' => 3];
            $playerData->bid = null;
            $playerData->seat = $params[$playerId];
            $playerData->hand = $this->dealer->getEmptyStock();
            return $playerData;
        });

        $data4P = $this->getCollectionPlayerDataThousand(true);
        $data4P->each(function($playerData, $playerId) {
            $params = ['Id-0' => 1, 'Id-1' => 2, 'Id-2' => 3, 'Id-3' => 4];
            $playerData->bid = null;
            $playerData->seat = $params[$playerId];
            $playerData->hand = $this->dealer->getEmptyStock();
            return $playerData;
        });

        $steward3P = $this->steward;
        $steward4P = $this->getSteward(true);

        $steward3P->shuffleAndDealCards($gData3P, $data3P);
        $steward4P->shuffleAndDealCards($gData4P, $data4P);

        $this->assertEquals(0, $gData3P->deck->count());
        $this->assertEquals(0, $gData4P->deck->count());
        $this->assertEquals(3, $gData3P->stock->count());
        $this->assertEquals(3, $gData4P->stock->count());
        $this->assertEquals(7, $data3P->getOne('Id-0')->hand->count());
        $this->assertEquals(7, $data3P->getOne('Id-1')->hand->count());
        $this->assertEquals(7, $data3P->getOne('Id-2')->hand->count());
        $this->assertEquals(0, $data4P->getOne('Id-0')->hand->count());
        $this->assertEquals(7, $data4P->getOne('Id-1')->hand->count());
        $this->assertEquals(7, $data4P->getOne('Id-2')->hand->count());
        $this->assertEquals(7, $data4P->getOne('Id-3')->hand->count());
    }

    public function testGetTrickWinner(): void
    {
        $data3P = $this->getCollectionPlayerDataThousand();
        $data3P->each(function($playerData, $playerId) {
            $params = ['Id-0' => 1, 'Id-1' => 2, 'Id-2' => 3];
            $playerData->bid = null;
            $playerData->seat = $params[$playerId];
            $playerData->hand = $this->dealer->getEmptyStock();
            return $playerData;
        });

        $deck = $this->deckProvider->getDeckSchnapsen();

        $gData3P = new GameDataThousand();
        $gData3P->dealer = $this->getPlayersCollectionMock()->getOne('Id-0');
        $gData3P->turnLead = $gData3P->dealer;
        $gData3P->trumpSuit = $deck->getOne('A-H')->getSuit();
        $gData3P->turnSuit = $deck->getOne('A-D')->getSuit();

        $tableTrumpBeatsNotTrump = $this->dealer->getEmptyStock()->reset(
            [$deck->getOne('10-D'), $deck->getOne('A-D'), $deck->getOne('9-H')]
        );
        $tableTrumpBeatsTrumpRank = $this->dealer->getEmptyStock()->reset(
            [$deck->getOne('10-D'), $deck->getOne('10-H'), $deck->getOne('A-H')]
        );
        $tableTurnBeatsTurnRank = $this->dealer->getEmptyStock()->reset(
            [$deck->getOne('10-D'), $deck->getOne('K-C'), $deck->getOne('A-D')]
        );

        $gData3P->table = $tableTrumpBeatsNotTrump;
        $this->assertEquals('Id-2', $this->steward->getTrickWinner($gData3P, $data3P)->getId());

        $gData3P->table = $tableTrumpBeatsTrumpRank;
        $this->assertEquals('Id-2', $this->steward->getTrickWinner($gData3P, $data3P)->getId());

        $gData3P->table = $tableTurnBeatsTurnRank;
        $this->assertEquals('Id-2', $this->steward->getTrickWinner($gData3P, $data3P)->getId());
    }

    public function testHasPlayerUsedMaxBombMoves(): void
    {
        $this->steward = $this->getSteward(false, 2, null);

        $this->assertTrue($this->steward->hasPlayerUsedMaxBombMoves([1, 4]));
        $this->assertFalse($this->steward->hasPlayerUsedMaxBombMoves([2]));
        $this->assertFalse($this->steward->hasPlayerUsedMaxBombMoves([]));
    }

    public function testHasMarriageAtHand(): void
    {
        $deck = $this->deckProvider->getDeckSchnapsen();

        $hand1 = $this->dealer->getCardsByKeys($deck, ['A-H', '10-H', 'K-H', 'Q-H', 'J-H', '9-H', 'Q-S'], true, true);
        $hand2 = $this->dealer->getCardsByKeys($deck, ['A-D', '10-D', 'K-D', 'Q-C', 'J-D', '9-D', '10-S'], true, true);
        $hand3 = $this->dealer->getCardsByKeys($deck, ['A-C', '10-C', 'J-S', 'A-S', 'J-C', '9-C', '9-S'], true, true);

        $this->assertTrue($this->steward->hasMarriageAtHand($hand1));
        $this->assertFalse($this->steward->hasMarriageAtHand($hand2));
        $this->assertFalse($this->steward->hasMarriageAtHand($hand3));
    }

    public function testCountPlayedPoints(): void
    {
        $deck = $this->deckProvider->getDeckSchnapsen();
        $tricks0 = $this->dealer->getCardsByKeys($deck, ['A-H', '10-H', 'K-H', 'Q-H', 'J-H', '9-H', 'Q-S'], true, true);
        $tricks1 = $this->dealer->getCardsByKeys($deck, ['A-D', '10-D', 'K-D', 'Q-C', 'J-D', '9-D', '10-S'], true, true);
        $tricks2 = $this->dealer->getCardsByKeys($deck, ['A-C', '10-C', 'J-S', 'A-S', 'J-C', '9-C', '9-S'], true, true);

        $trumps0 = ['K-H'];
        $trumps1 = [];
        $trumps2 = [];

        $this->assertEquals(100, $this->steward->countPlayedPoints(true, 100, $tricks0, $trumps0));
        $this->assertEquals(-120, $this->steward->countPlayedPoints(true, 120, $tricks1, $trumps1));
        $this->assertEquals(130, $this->steward->countPlayedPoints(false, 100, $tricks0, $trumps0));
        $this->assertEquals(40, $this->steward->countPlayedPoints(false, 100, $tricks2, $trumps2));
    }

    public function testSetBarrelStatus(): void
    {
        $data = $this->getCollectionPlayerDataThousand(true);
        $data->each(function($playerData, $playerId) {
            $points = ['Id-0' => [1 => 800], 'Id-1' => [1 => 790], 'Id-2' => [1 => 900],  'Id-3' => []];
            $playerData->points = $points[$playerId];
            return $playerData;
        });

        $this->getSteward(false, null, 800)->setBarrelStatus($data->getOne('Id-0'));
        $this->getSteward(false, null, 800)->setBarrelStatus($data->getOne('Id-1'));
        $this->getSteward(false, null, 0)->setBarrelStatus($data->getOne('Id-2'));
        $this->getSteward(false, null, 0)->setBarrelStatus($data->getOne('Id-3'));

        $dataArray = $data->toArray();

        $this->assertTrue($dataArray['Id-0']->barrel);
        $this->assertFalse($dataArray['Id-1']->barrel);
        $this->assertFalse($dataArray['Id-2']->barrel);
        $this->assertFalse($dataArray['Id-3']->barrel);
    }

    // TODO write test for setRoundPointsWithSomeDifferentOptions to test (Stock marriage, aces, trumps, bid achieved/not, player on barrel or not, 4 players dealer etc.)
}
