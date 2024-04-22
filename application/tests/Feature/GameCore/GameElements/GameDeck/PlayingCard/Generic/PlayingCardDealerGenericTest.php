<?php

namespace GameCore\GameElements\GameDeck\PlayingCard\Generic;

use App\GameCore\GameElements\GameDeck\PlayingCard\CollectionPlayingCard;
use App\GameCore\GameElements\GameDeck\PlayingCard\CollectionPlayingCardUnique;
use App\GameCore\GameElements\GameDeck\PlayingCard\Generic\PlayingCardDealerGeneric;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDealer;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDealerException;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDeckProvider;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class PlayingCardDealerGenericTest extends TestCase
{
    private PlayingCardDealerGeneric $dealer;
    private PlayingCardDeckProvider $deckProvider;

    public function setUp(): void
    {
        parent::setUp();
        $this->dealer = App::make(PlayingCardDealer::class);
        $this->deckProvider = App::make(PlayingCardDeckProvider::class);
    }

    public function testInterface(): void
    {
        $this->assertInstanceOf(PlayingCardDealer::class, $this->dealer);
    }

    public function testGetEmptyStock(): void
    {
        $stockUnique = $this->dealer->getEmptyStock();
        $stockNotUnique = $this->dealer->getEmptyStock(false);

        $this->assertInstanceOf(CollectionPlayingCardUnique::class, $stockUnique);
        $this->assertInstanceOf(CollectionPlayingCard::class, $stockNotUnique);
        $this->assertNotInstanceOf(CollectionPlayingCardUnique::class, $stockNotUnique);
        $this->assertEquals(0, $stockUnique->count());
        $this->assertEquals(0, $stockNotUnique->count());
    }

    public function testThrowExceptionShuffleAndDealCardsEmptyDistributionDefinition(): void
    {
        $this->expectException(PlayingCardDealerException::class);
        $this->expectExceptionMessage(PlayingCardDealerException::MESSAGE_DISTRIBUTION_DEFINITION);

        $this->dealer->shuffleAndDealCards($this->deckProvider->getDeckSchnapsen(), []);
    }

    public function testThrowExceptionShuffleAndDealCardsInvalidDistributionDefinitionStock(): void
    {
        $this->expectException(PlayingCardDealerException::class);
        $this->expectExceptionMessage(PlayingCardDealerException::MESSAGE_DISTRIBUTION_DEFINITION);

        $definition = [['stock' => 'invalid', 'numberOfCards' => 1]];
        $this->dealer->shuffleAndDealCards($this->deckProvider->getDeckSchnapsen(), $definition);
    }

    public function testThrowExceptionShuffleAndDealCardsInvalidDistributionDefinitionNumber(): void
    {
        $this->expectException(PlayingCardDealerException::class);
        $this->expectExceptionMessage(PlayingCardDealerException::MESSAGE_DISTRIBUTION_DEFINITION);

        $stock = $this->dealer->getEmptyStock(false);
        $definition = [['stock' => $stock, 'numberOfCards' => 'invalid']];

        $this->dealer->shuffleAndDealCards($this->deckProvider->getDeckSchnapsen(), $definition);
    }

    public function testThrowExceptionShuffleAndDealCardsNotEnoughInDeck(): void
    {
        $this->expectException(PlayingCardDealerException::class);
        $this->expectExceptionMessage(PlayingCardDealerException::MESSAGE_NOT_ENOUGH_TO_DEAL);

        $deck = $this->deckProvider->getDeckSchnapsen();
        $stock = $this->dealer->getEmptyStock();
        $definition = [['stock' => $stock, 'numberOfCards' => $deck->count() + 1]];

        $this->dealer->shuffleAndDealCards($deck, $definition);
    }

    public function testShuffleAndDealCardsShapsenDefinition(): void
    {
        $deck = $this->deckProvider->getDeckSchnapsen();
        $stockP1 = $this->dealer->getEmptyStock();
        $stockP2 = $this->dealer->getEmptyStock();
        $stockP3 = $this->dealer->getEmptyStock();
        $stockD = $this->dealer->getEmptyStock();
        $definition = [
            ['stock' => $stockP1, 'numberOfCards' => 7],
            ['stock' => $stockP2, 'numberOfCards' => 7],
            ['stock' => $stockP3, 'numberOfCards' => 7],
            ['stock' => $stockD, 'numberOfCards' => 3],
        ];
        $this->dealer->shuffleAndDealCards($deck, $definition);

        $this->assertEquals(7, $stockP1->count());
        $this->assertEquals(7, $stockP2->count());
        $this->assertEquals(7, $stockP3->count());
        $this->assertEquals(3, $stockD->count());
        $this->assertEquals(0, $deck->count());
    }

    public function testShuffleAndDealCardsNulls(): void
    {
        $deck = $this->deckProvider->getDeckSchnapsen();
        $stockP1 = $this->dealer->getEmptyStock();
        $stockP2 = $this->dealer->getEmptyStock();
        $definition = [
            ['stock' => $stockP1, 'numberOfCards' => null],
            ['stock' => $stockP2, 'numberOfCards' => null],
        ];
        $this->dealer->shuffleAndDealCards($deck, $definition);

        $this->assertEquals(12, $stockP1->count());
        $this->assertEquals(12, $stockP2->count());
        $this->assertEquals(0, $deck->count());
    }

    public function testShuffleAndDealCardsNumbersAndNullsTogether(): void
    {
        $deck = $this->deckProvider->getDeckSchnapsen();
        $stockP1 = $this->dealer->getEmptyStock();
        $stockP2 = $this->dealer->getEmptyStock();
        $stockP3 = $this->dealer->getEmptyStock();
        $stockD = $this->dealer->getEmptyStock();
        $definition = [
            ['stock' => $stockP1, 'numberOfCards' => 7],
            ['stock' => $stockP2, 'numberOfCards' => null],
            ['stock' => $stockP3, 'numberOfCards' => null],
            ['stock' => $stockD, 'numberOfCards' => 3],
        ];
        $this->dealer->shuffleAndDealCards($deck, $definition);

        $this->assertEquals(7, $stockP1->count());
        $this->assertEquals(7, $stockP2->count());
        $this->assertEquals(7, $stockP3->count());
        $this->assertEquals(3, $stockD->count());
        $this->assertEquals(0, $deck->count());
    }

    public function testShuffleAndDealCardsReturnRandomCards(): void
    {
        $stock1 = $this->dealer->getEmptyStock();
        $stock2 = $this->dealer->getEmptyStock();
        $stock3 = $this->dealer->getEmptyStock();
        $definition1 = [['stock' => $stock1, 'numberOfCards' => null]];
        $definition2 = [['stock' => $stock2, 'numberOfCards' => null]];
        $definition3 = [['stock' => $stock3, 'numberOfCards' => null]];

        $this->dealer->shuffleAndDealCards($this->deckProvider->getDeckSchnapsen(), $definition1);
        $this->dealer->shuffleAndDealCards($this->deckProvider->getDeckSchnapsen(), $definition2);
        $this->dealer->shuffleAndDealCards($this->deckProvider->getDeckSchnapsen(), $definition3);

        $this->assertNotEquals(array_keys($stock1->toArray()), array_keys($stock2->toArray()));
        $this->assertNotEquals(array_keys($stock1->toArray()), array_keys($stock3->toArray()));
        $this->assertNotEquals(array_keys($stock3->toArray()), array_keys($stock2->toArray()));
    }

    public function testThrowExceptionWhenMoveCardTimesNotEnoughCards(): void
    {
        $this->expectException(PlayingCardDealerException::class);
        $this->expectExceptionMessage(PlayingCardDealerException::MESSAGE_NOT_ENOUGH_IN_STOCK);

        $fromStock = $this->deckProvider->getDeckSchnapsen();
        $toStock = $this->dealer->getEmptyStock();
        $this->dealer->moveCardsTimes($fromStock, $toStock, 25, true);
    }

    public function testMoveCardsTimesNotZeroingStock(): void
    {
        $fromStock = $this->deckProvider->getDeckSchnapsen();
        $toStock = $this->dealer->getEmptyStock();
        $initialFromStockCount = $fromStock->count();
        $numberOfCards = 7;
        $this->dealer->moveCardsTimes($fromStock, $toStock, $numberOfCards, false);

        $this->assertEquals($initialFromStockCount - $numberOfCards, $fromStock->count());
        $this->assertEquals($numberOfCards, $toStock->count());
    }

    public function testMoveCardsTimesZeroingStock(): void
    {
        $fromStock = $this->deckProvider->getDeckSchnapsen();
        $toStock = $this->dealer->getEmptyStock();
        $initialFromStockCount = $fromStock->count();
        $numberOfCards = $initialFromStockCount + 1;
        $this->dealer->moveCardsTimes($fromStock, $toStock, $numberOfCards, false);

        $this->assertEquals(0, $fromStock->count());
        $this->assertEquals($initialFromStockCount, $toStock->count());
    }

    public function testThrowExceptionWhenPullFirstStrictFromEmptyStock(): void
    {
        $this->expectException(PlayingCardDealerException::class);
        $this->expectExceptionMessage(PlayingCardDealerException::MESSAGE_NOT_ENOUGH_IN_STOCK);

        $stock = $this->dealer->getEmptyStock();
        $this->dealer->pullFirstCard($stock, true);
    }

    public function testPullFirstReturnNullOnEmptyStock(): void
    {
        $stock = $this->dealer->getEmptyStock();
        $this->assertNull($this->dealer->pullFirstCard($stock));
    }

    public function testPullFirst(): void
    {
        $deck = $this->deckProvider->getDeckSchnapsen();
        $initialDeckCount = $deck->count();
        $card = $this->dealer->pullFirstCard($deck);

        $this->assertEquals($initialDeckCount - 1, $deck->count());
        $this->assertFalse($deck->exist($card->getKey()));
    }

    public function testThrowExceptionWhenGetCardsByKeysUniqueFlagRepeatedKeys(): void
    {
        $this->expectException(PlayingCardDealerException::class);
        $this->expectExceptionMessage(PlayingCardDealerException::MESSAGE_NOT_UNIQUE_KEYS);

        $deck = $this->deckProvider->getDeckSchnapsen();
        $key = array_keys($deck->toArray())[0];

        $this->dealer->getCardsByKeys($deck, [$key, $key], true);
    }

    public function testThrowExceptionWhenGetCardsByKeysStrictMissingKeys(): void
    {
        $this->expectException(PlayingCardDealerException::class);
        $this->expectExceptionMessage(PlayingCardDealerException::MESSAGE_KEY_MISSING_IN_STOCK);

        $deck = $this->deckProvider->getDeckSchnapsen();
        $key = ['2-H'];

        $this->dealer->getCardsByKeys($deck, [$key], false, true);
    }

    public function testGetCardsByKeysWithUnique(): void
    {
        $deck = $this->deckProvider->getDeckSchnapsen();
        $keys = [$deck->pullFirst()->getKey(), $deck->pullFirst()->getKey()];
        $stock = $this->dealer->getCardsByKeys($this->deckProvider->getDeckSchnapsen(), $keys, true);

        $this->assertInstanceOf(CollectionPlayingCardUnique::class, $stock);
        $this->assertEquals(2, $stock->count());
    }

    public function testGetCardsByKeysWithoutUnique(): void
    {
        $deck = $this->deckProvider->getDeckSchnapsen();
        $key = $deck->pullFirst()->getKey();
        $stock = $this->dealer->getCardsByKeys($this->deckProvider->getDeckSchnapsen(), [$key, $key]);

        $this->assertInstanceOf(CollectionPlayingCard::class, $stock);
        $this->assertEquals(2, $stock->count());
    }

    public function testGetCardsByKeyWithoutUsingKeys(): void
    {
        $emptyStock = $this->dealer->getCardsByKeys($this->deckProvider->getDeckSchnapsen(), []);
        $nullStock = $this->dealer->getCardsByKeys($this->deckProvider->getDeckSchnapsen(), null);

        $this->assertEquals(0, $emptyStock->count());
        $this->assertEquals(0, $nullStock->count());
    }

    public function testGetCardsKeys(): void
    {
        $deck = $this->deckProvider->getDeckSchnapsen();
        $keys = [$deck->pullFirst()->getKey(), $deck->pullFirst()->getKey(), $deck->pullFirst()->getKey()];
        $stock = $this->dealer->getCardsByKeys($this->deckProvider->getDeckSchnapsen(), $keys, true);
        $emptyStock = $this->dealer->getCardsByKeys($this->deckProvider->getDeckSchnapsen(), [], true);

        $this->assertEquals($keys, $this->dealer->getCardsKeys($stock));
        $this->assertEquals([], $this->dealer->getCardsKeys($emptyStock));
    }
}
