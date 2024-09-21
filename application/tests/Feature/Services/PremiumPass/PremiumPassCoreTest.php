<?php

namespace Tests\Feature\Services\PremiumPass;

use App\Services\PremiumPass\PremiumPass;
use App\Services\PremiumPass\PremiumPassCore;
use App\Services\PremiumPass\PremiumPassException;
use MyDramGames\Core\GameBox\GameBox;
use MyDramGames\Core\GameBox\GameBoxRepository;
use MyDramGames\Utils\Player\Player;
use MyDramGames\Utils\Player\PlayerAnonymous;
use MyDramGames\Utils\Player\PlayerRegistered;
use Tests\TestCase;

class PremiumPassCoreTest extends TestCase
{
    private PremiumPassCore $premiumPass;
    private GameBoxRepository $gameBoxRepository;
    private Player $playerRegular;
    private Player $playerPremium;
    private string $slugRegular = 'regular';
    private string $slugPremium = 'premium';

    public function setUp(): void
    {
        parent::setUp();

        $this->playerRegular = $this->createMock(PlayerAnonymous::class);
        $this->playerRegular->method('isPremium')->willReturn(false);

        $this->playerPremium = $this->createMock(PlayerRegistered::class);
        $this->playerPremium->method('isPremium')->willReturn(true);

        $this->premiumPassSetup();
    }

    protected function getGameBoxRepositoryMock(bool $premium = false): GameBoxRepository
    {
        $boxRegular = $this->createMock(GameBox::class);
        $boxRegular->method('isPremium')->willReturn(false);

        $boxPremium = $this->createMock(GameBox::class);
        $boxPremium->method('isPremium')->willReturn(true);

        $repository = $this->createMock(GameBoxRepository::class);
        $repository->method('getOne')
            ->with($premium ? $this->slugPremium : $this->slugRegular)
            ->willReturn($premium ? $boxPremium : $boxRegular);

        return $repository;
    }

    protected function premiumPassSetup(bool $premium = false): void
    {
        $this->gameBoxRepository = $this->getGameBoxRepositoryMock($premium);
        $this->premiumPass = new PremiumPassCore($this->gameBoxRepository);
    }

    public function testInterface(): void
    {
        $this->assertInstanceOf(PremiumPass::class, $this->premiumPass);
    }

    public function testThrowExceptionValidatingPremiumGameForRegularUser(): void
    {
        $this->expectException(PremiumPassException::class);
        $this->expectExceptionMessage(PremiumPassException::MESSAGE_MISSING_PREMIUM_PASS);

        $this->premiumPassSetup(true);
        $this->premiumPass->validate($this->slugPremium, $this->playerRegular);
    }

    public function testNoExceptionValidatingPremiumGameForPremiumUser(): void
    {
        $this->expectNotToPerformAssertions();

        $this->premiumPassSetup(true);
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
