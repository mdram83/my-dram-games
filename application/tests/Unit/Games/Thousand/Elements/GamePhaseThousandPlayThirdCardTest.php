<?php

namespace Games\Thousand\Elements;

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

class GamePhaseThousandPlayThirdCardTest extends TestCase
{
    private GamePhaseThousandPlayThirdCard $phase;

    public function setUp(): void
    {
        parent::setUp();
        $this->phase = new GamePhaseThousandPlayThirdCard();
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
        $this->assertInstanceOf(GamePhaseThousandPlayFirstCard::class, $this->phase->getNextPhase(false));
        $this->assertInstanceOf(GamePhaseThousandCountPoints::class, $this->phase->getNextPhase(true));
    }
}
