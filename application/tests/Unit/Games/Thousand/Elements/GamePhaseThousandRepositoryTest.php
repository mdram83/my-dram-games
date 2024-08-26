<?php

namespace Tests\Unit\Games\Thousand\Elements;

use App\GameCore\GameElements\GamePhase\GamePhaseException;
use App\Games\Thousand\Elements\GamePhaseThousand;
use App\Games\Thousand\Elements\GamePhaseThousandBidding;
use App\Games\Thousand\Elements\GamePhaseThousandRepository;
use PHPUnit\Framework\TestCase;

class GamePhaseThousandRepositoryTest extends TestCase
{
    private GamePhaseThousandRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new GamePhaseThousandRepository();
    }

    public function testObjectCreated(): void
    {
        $this->assertInstanceOf(GamePhaseThousandRepository::class, $this->repository);
    }

    public function testGetOneFromPhaseKey(): void
    {
        $phase = $this->repository->getOne(GamePhaseThousandBidding::PHASE_KEY);
        $this->assertInstanceOf(GamePhaseThousand::class, $phase);
    }

    public function testThrowExceptionWhenGettingWrongPhaseKey(): void
    {
        $this->expectException(GamePhaseException::class);
        $this->expectExceptionMessage(GamePhaseException::MESSAGE_INCORRECT_KEY);

        $this->repository->getOne('missing-key');
    }
}
