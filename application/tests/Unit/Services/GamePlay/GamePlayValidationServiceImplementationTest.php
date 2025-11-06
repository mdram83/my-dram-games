<?php

namespace Services\GamePlay;

use App\Services\GamePlay\GamePlayValidationServiceImplementation;
use MyDramGames\Core\GamePlay\GamePlay;
use MyDramGames\Utils\Player\Player;
use MyDramGames\Utils\Player\PlayerCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class GamePlayValidationServiceImplementationTest extends TestCase
{
    private GamePlayValidationServiceImplementation $gamePlayValidationServiceImplementation;
    private Player $player;
    private PlayerCollection $playerCollection;
    private GamePlay $gamePlay;

    public function setUp(): void
    {
        $this->gamePlayValidationServiceImplementation = new GamePlayValidationServiceImplementation();

        $this->player = $this->createMock(Player::class);
        $this->player->method('getId')->willReturn(1);
    }

    private function setPlayerCollection(bool $existReturnValue): void
    {
        $this->playerCollection = $this->createMock(PlayerCollection::class);
        $this->playerCollection->method('exist')->willReturn($existReturnValue);
    }

    private function setGamePlay(): void
    {
        $this->gamePlay = $this->createMock(GamePlay::class);
        $this->gamePlay->method('getPlayers')->willReturn($this->playerCollection);
    }

    public function testValidateGamePlayPLayerNotPlayerThrowsAccessDeniedException(): void
    {
        $this->expectException(AccessDeniedHttpException::class);
        $this->setPlayerCollection(false);
        $this->setGamePlay();

        $this->gamePlayValidationServiceImplementation->validateGamePlayPlayer($this->gamePlay, $this->player);
    }

    public function testValidateGamePlayPLayerPlayerNoException(): void
    {
        $this->expectNotToPerformAssertions();

        $this->setPlayerCollection(true);
        $this->setGamePlay();

        $this->gamePlayValidationServiceImplementation->validateGamePlayPlayer($this->gamePlay, $this->player);
    }
}
