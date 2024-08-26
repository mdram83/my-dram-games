<?php

namespace Tests\Feature\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum;

use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardSuitPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardSuitRepositoryPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardException;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardSuitRepository;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class PlayingCardSuitRepositoryPhpEnumTest extends TestCase
{
    private PlayingCardSuitRepositoryPhpEnum $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = App::make(PlayingCardSuitRepository::class);
    }

    public function testInterfaceInstance(): void
    {
        $this->assertInstanceOf(PlayingCardSuitRepository::class, $this->repository);
    }

    public function testThrowExceptionWhenGettingMissingKey(): void
    {
        $this->expectException(PlayingCardException::class);
        $this->expectExceptionMessage(PlayingCardException::MESSAGE_MISSING_SUIT);

        $this->repository->getOne('definitely-123-missing456-suit-key');
    }

    public function testGetOne(): void
    {
         $suit = PlayingCardSuitPhpEnum::cases()[0];
         $this->assertEquals($suit, $this->repository->getOne($suit->getKey()));
    }
}
