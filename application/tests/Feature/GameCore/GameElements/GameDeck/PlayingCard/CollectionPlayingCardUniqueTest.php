<?php

namespace Tests\Feature\GameCore\GameElements\GameDeck\PlayingCard;

use App\GameCore\GameElements\GameDeck\PlayingCard\CollectionPlayingCardUnique;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDeckProvider;
use App\GameCore\Services\Collection\Collection;
use App\GameCore\Services\Collection\CollectionException;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class CollectionPlayingCardUniqueTest extends TestCase
{
    private PlayingCardDeckProvider $deckProvider;
    private Collection $deck;
    private CollectionPlayingCardUnique $collection;

    public function setUp(): void
    {
        parent::setUp();
        $this->deckProvider = App::make(PlayingCardDeckProvider::class);
        $this->deck = $this->deckProvider->getDeckSchnapsen();
        $this->collection = new CollectionPlayingCardUnique(App::make(Collection::class));
    }

    public function testGetOneWithCardKey(): void
    {
        $card = $this->deck->pullFirst();
        $this->collection->add($card);

        $this->assertEquals($card->getKey(), $this->collection->getOne($card->getKey())->getKey());
    }

    public function testThrowExceptionWhenAddingSameKeyTwice(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_DUPLICATE);

        $card = $this->deck->pullFirst();
        $this->collection->add($card);
        $this->collection->add($card);
    }

    public function testThrowExceptionWhenAddingNotCard(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_INCOMPATIBLE);

        $this->collection->add('wrong-type');
    }

    public function testThrowExceptionWhenResetWithDuplicates(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_DUPLICATE);

        $card = $this->deck->pullFirst();
        $this->collection->reset([$card, $card]);
    }

    public function testResetWithoutDuplicatesOk(): void
    {
        $card1 = $this->deck->pullFirst();
        $card2 = $this->deck->pullFirst();
        $this->collection->reset([$card1, $card2]);

        $this->assertEquals(2, $this->collection->count());
    }

    public function testCreateWithoutDuplicatesOk(): void
    {
        $card1 = $this->deck->pullFirst();
        $card2 = $this->deck->pullFirst();
        $collection = new CollectionPlayingCardUnique(App::make(Collection::class), [$card1, $card2]);

        $this->assertEquals(2, $collection->count());
    }

    public function testThrowExceptionWhenCreateWithDuplicates(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_DUPLICATE);

        $card1 = $this->deck->pullFirst();
        new CollectionPlayingCardUnique(App::make(Collection::class), [$card1, $card1]);
    }

    public function testThrowExceptionWhenResetNotCard(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_INCOMPATIBLE);

        $this->collection->reset(['wrong-type']);
    }

    public function testThrowExceptionWhenCreateNotCard(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_INCOMPATIBLE);

        new CollectionPlayingCardUnique(App::make(Collection::class), ['not-compatible']);
    }
}
