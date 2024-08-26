<?php

namespace Tests\Feature\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum;

use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardRankPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardRankRepositoryPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardException;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardRankRepository;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class PlayingCardRankRepositoryPhpEnumTest extends TestCase
{
    private PlayingCardRankRepositoryPhpEnum $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = App::make(PlayingCardRankRepository::class);
    }

    public function testInterfaceInstance(): void
    {
        $this->assertInstanceOf(PlayingCardRankRepository::class, $this->repository);
    }

    public function testThrowExceptionWhenGettingMissingKey(): void
    {
        $this->expectException(PlayingCardException::class);
        $this->expectExceptionMessage(PlayingCardException::MESSAGE_MISSING_RANK);

        $this->repository->getOne('definitely-123-missing456-rank-key');
    }

    public function testGetOne(): void
    {
         $rank = PlayingCardRankPhpEnum::cases()[0];
         $this->assertEquals($rank, $this->repository->getOne($rank->getKey()));
    }
}
