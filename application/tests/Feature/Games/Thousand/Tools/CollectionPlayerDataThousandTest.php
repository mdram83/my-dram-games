<?php

namespace Games\Thousand\Tools;

use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\Collection;
use App\GameCore\Services\Collection\CollectionException;
use App\Games\Thousand\Tools\CollectionPlayerDataThousand;
use App\Games\Thousand\Tools\PlayerDataThousand;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class CollectionPlayerDataThousandTest extends TestCase
{
    private CollectionPlayerDataThousand $collection;
    private Collection $handler;
    private array $players;
    private array $playersData;

    public function setUp(): void
    {
        parent::setUp();
        $this->handler = App::make(Collection::class);
        $this->collection = new CollectionPlayerDataThousand(clone $this->handler);

        for ($i = 0; $i <= 2; $i++) {
            $player = $this->createMock(Player::class);
            $player->method('getId')->willReturn("Id-$i");

            $this->players[$i] = $player;
            $this->playersData[$i] = new PlayerDataThousand($player);
        }
    }

    public function testInterface(): void
    {
        $this->assertInstanceOf(Collection::class, $this->collection);
    }

    public function testThrowExceptionWhenAddingNotPlayerDataByConstructor(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_INCOMPATIBLE);

        new CollectionPlayerDataThousand(clone $this->handler, $this->players);
    }

    public function testThrowExceptionWhenAddingNotPlayerDataByAdd(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_INCOMPATIBLE);

        $this->collection->add($this->players[0]);
    }

    public function testThrowExceptionWhenAddingNotPlayerDataByReset(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_INCOMPATIBLE);

        $this->collection->reset($this->players);
    }

    public function testThrowExceptionWhenAddingSameByConstructor(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_DUPLICATE);

        new CollectionPlayerDataThousand(clone $this->handler, [$this->playersData[0], $this->playersData[0]]);
    }

    public function testThrowExceptionWhenAddingSameByAdd(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_DUPLICATE);

        $this->collection->add($this->playersData[0]);
        $this->collection->add($this->playersData[0]);
    }

    public function testThrowExceptionWhenAddingSameByReset(): void
    {
        $this->expectException(CollectionException::class);
        $this->expectExceptionMessage(CollectionException::MESSAGE_DUPLICATE);

        $this->collection->reset([$this->playersData[0], $this->playersData[0]]);
    }

    public function testGetOne(): void
    {
        $this->collection->add($this->playersData[0]);
        $this->assertInstanceOf(PlayerDataThousand::class, $this->collection->getOne($this->players[0]->getId()));
    }

    public function testGetFor(): void
    {
        $this->collection->add($this->playersData[0]);
        $this->assertInstanceOf(PlayerDataThousand::class, $this->collection->getFor($this->players[0]));
    }

    public function testToArray(): void
    {
        $this->collection->reset($this->playersData);
        $this->assertEquals(
            array_map(fn($player) => $player->getId(), $this->players),
            array_keys($this->collection->toArray())
        );
    }
}
