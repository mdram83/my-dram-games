<?php

namespace Games\Thousand\Tools;

use App\GameCore\GameElements\GameDeck\PlayingCard\CollectionPlayingCardUnique;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCard;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDealer;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDeckProvider;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardRank;
use App\GameCore\GameElements\GamePlayPlayers\CollectionGamePlayPlayers;
use App\GameCore\GameInvite\GameInvite;
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
        $this->steward = $this->getSteward();
        $this->deckProvider = App::make(PlayingCardDeckProvider::class);
        $this->dealer = App::make(PlayingCardDealer::class);
        $this->handler = App::make(Collection::class);
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

    private function getGameInviteMock(bool $fourPlayers = false): GameInvite
    {
        $invite = $this->createMock(GameInvite::class);

        return $invite;
    }

    private function getSteward(bool $fourPlayers = false): GameStewardThousand
    {
        return new GameStewardThousand(
            $this->getPlayersCollectionMock($fourPlayers),
            $this->getGameInviteMock($fourPlayers),
            App::make(PlayingCardDealer::class)
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
}
