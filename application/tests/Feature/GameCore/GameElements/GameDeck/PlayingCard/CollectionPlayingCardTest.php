<?php

namespace Tests\Feature\GameCore\GameElements\GameDeck\PlayingCard;

use App\GameCore\GameElements\GameDeck\PlayingCard\CollectionPlayingCard;
use App\GameCore\GameElements\GameDeck\PlayingCard\Generic\PlayingCardGeneric;
use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardRankPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardSuitPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardFactory;
use App\GameCore\Services\Collection\Collection;
use App\GameCore\Services\Collection\CollectionException;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class CollectionPlayingCardTest extends TestCase
{
    private array $cards;
    private string $noCard = 'test-no-card-element';
    private Collection $handler;

    public function setUp(): void
    {
        parent::setUp();

        $factory = App::make(PlayingCardFactory::class);
        $card = $factory->create(
            PlayingCardRankPhpEnum::Ace->getKey()
            . PlayingCardGeneric::PLAYING_CARD_KEY_SEPARATOR
            . PlayingCardSuitPhpEnum::Hearts->getKey()
        );

        $this->cards = [clone $card, clone $card];
        $this->handler = App::make(Collection::class);
    }

    public function testInterfaceInstance(): void
    {
        $this->assertInstanceOf(Collection::class, new CollectionPlayingCard($this->handler));
    }

    public function testThrowExceptionWhenAddingNoCard(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_INCOMPATIBLE);

        $collection = new CollectionPlayingCard($this->handler);
        $collection->add($this->noCard);
    }

    public function testThrowExceptionWhenResetNoCard(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_INCOMPATIBLE);

        $collection = new CollectionPlayingCard($this->handler);
        $collection->reset([$this->noCard]);
    }

    public function testThrowExceptionWhenCreateWithNoCard(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_INCOMPATIBLE);

        new CollectionPlayingCard($this->handler, [$this->noCard]);
    }

    public function testAddCards(): void
    {
        $collection = new CollectionPlayingCard($this->handler);
        $collection->add($this->cards[0]);
        $collection->add($this->cards[1]);

        $this->assertEquals(2, $collection->count());
    }

    public function testResetCards(): void
    {
        $collection = new CollectionPlayingCard($this->handler);
        $collection->add($this->cards[0]);
        $collection->reset($this->cards);

        $this->assertEquals(2, $collection->count());
    }

    public function testCreateCards(): void
    {
        $collection = new CollectionPlayingCard($this->handler, $this->cards);
        $this->assertEquals(2, $collection->count());
    }
}
