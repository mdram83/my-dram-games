<?php

namespace Tests\Unit\Games\Thousand\Elements;

use App\GameCore\GameElements\GamePhase\GamePhaseException;
use App\Games\Thousand\Elements\GamePhaseThousand;
use App\Games\Thousand\Elements\GamePhaseThousandBidding;
use App\Games\Thousand\Elements\GamePhaseThousandCountPoints;
use App\Games\Thousand\Elements\GamePhaseThousandDeclaration;
use App\Games\Thousand\Elements\GamePhaseThousandPlayFirstCard;
use App\Games\Thousand\Elements\GamePhaseThousandPlaySecondCard;
use App\Games\Thousand\Elements\GamePhaseThousandPlayThirdCard;
use App\Games\Thousand\Elements\GamePhaseThousandStockDistribution;
use PHPUnit\Framework\TestCase;

class GamePhaseThousandCountPointsTest extends TestCase
{
    private GamePhaseThousandCountPoints $phase;

    public function setUp(): void
    {
        parent::setUp();
        $this->phase = new GamePhaseThousandCountPoints();
    }

    public function testGetKey(): void
    {
        $this->assertEquals($this->phase::PHASE_KEY, $this->phase->getKey());
        $this->assertNotNull($this->phase->getKey());
        $this->assertNotEquals('', $this->phase->getKey());
    }

    public function testGetName(): void
    {
        $this->assertNotNull($this->phase->getName());
        $this->assertNotEquals('', $this->phase->getName());
    }

    public function testGetDescription(): void
    {
        $this->assertNotNull($this->phase->getDescription());
        $this->assertNotEquals('', $this->phase->getDescription());
    }

    public function testGetNextPhase(): void
    {
        $this->assertInstanceOf(GamePhaseThousandBidding::class, $this->phase->getNextPhase(true));
        $this->assertInstanceOf($this->phase::class, $this->phase->getNextPhase(false));
    }
}
