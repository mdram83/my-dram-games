<?php

namespace GameCore\Services\PremiumPass\Basic;

use App\GameCore\Player\Player;
use App\GameCore\Player\PlayerAnonymous;
use App\GameCore\Player\PlayerRegistered;
use App\GameCore\Services\PremiumPass\Basic\PremiumPassBasic;
use App\GameCore\Services\PremiumPass\PremiumPass;
use App\GameCore\Services\PremiumPass\PremiumPassException;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class PremiumPassBasicTest extends TestCase
{
    private PremiumPassBasic $premiumPass;
    private Player $playerRegular;
    private Player $playerPremium;
    private string $slugRegular = 'tic-tac-toe';
    private string $slugPremium = 'thousand';

    public function setUp(): void
    {
        parent::setUp();
        $this->premiumPass = App::make(PremiumPass::class);

        $this->playerRegular = $this->createMock(PlayerAnonymous::class);
        $this->playerRegular->method('isPremium')->willReturn(false);

        $this->playerPremium = $this->createMock(PlayerRegistered::class);
        $this->playerPremium->method('isPremium')->willReturn(true);
    }

    public function testInterface(): void
    {
        $this->assertInstanceOf(PremiumPass::class, $this->premiumPass);
    }

    public function testThrowExceptionValidatingPremiumGameForRegularUser(): void
    {
        $this->expectException(PremiumPassException::class);
        $this->expectExceptionMessage(PremiumPassException::MESSAGE_MISSING_PREMIUM_PASS);

        $this->premiumPass->validate($this->slugPremium, $this->playerRegular   );
    }

    public function testNoExceptionValidatingPremiumGameForPremiumUser(): void
    {
        $this->expectNotToPerformAssertions();
        $this->premiumPass->validate($this->slugPremium, $this->playerPremium);
    }

    public function testNoExceptionValidatingRegularGameForRegularUser(): void
    {
        $this->expectNotToPerformAssertions();
        $this->premiumPass->validate($this->slugRegular, $this->playerRegular);
    }

    public function testNoExceptionValidatingRegularGameForPremiumUser(): void
    {
        $this->expectNotToPerformAssertions();
        $this->premiumPass->validate($this->slugRegular, $this->playerPremium);
    }
}
