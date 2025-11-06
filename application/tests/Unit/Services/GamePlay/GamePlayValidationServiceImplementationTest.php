<?php

namespace Services\GamePlay;

use App\Services\GamePlay\GamePlayValidationServiceException;
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

    private function configureMocks(bool $existReturnValue = true, bool $isFinishedReturnValue = false): void
    {
        $this->setPlayerCollection($existReturnValue);
        $this->setGamePlay($isFinishedReturnValue);
    }

    private function setPlayerCollection(bool $existReturnValue = true): void
    {
        $this->playerCollection = $this->createMock(PlayerCollection::class);
        $this->playerCollection->method('exist')->willReturn($existReturnValue);
    }

    private function setGamePlay(bool $isFinishedReturnValue = false): void
    {
        $this->gamePlay = $this->createMock(GamePlay::class);
        $this->gamePlay->method('getPlayers')->willReturn($this->playerCollection);
        $this->gamePlay->method('isFinished')->willReturn($isFinishedReturnValue);
    }

    public function testValidateGamePlayPLayerNotPlayerThrowsAccessDeniedException(): void
    {
        $this->expectException(AccessDeniedHttpException::class);

        $this->configureMocks(false);
        $this->gamePlayValidationServiceImplementation->validateGamePlayPlayer($this->gamePlay, $this->player);
    }

    public function testValidateGamePlayPLayerPlayerNoException(): void
    {
        $this->expectNotToPerformAssertions();

        $this->configureMocks();
        $this->gamePlayValidationServiceImplementation->validateGamePlayPlayer($this->gamePlay, $this->player);
    }

    public function testValidateGamePlayNotFinishedDoesNotThrowsGamePlayValidationDoesNotException(): void
    {
        $this->expectNotToPerformAssertions();

        $this->configureMocks();
        $this->gamePlayValidationServiceImplementation->validateGamePlayNotFinished($this->gamePlay);
    }

    public function testValidateGamePlayNotFinishedThrowsException(): void
    {
        $this->expectException(GamePlayValidationServiceException::class);
        $this->expectExceptionMessage(GamePlayValidationServiceException::MESSAGE_FINISHED);

        $this->configureMocks(true, true);
        $this->gamePlayValidationServiceImplementation->validateGamePlayNotFinished($this->gamePlay);
    }

    public function testValidateDisconnectionApplicableNoException(): void
    {
        $this->expectNotToPerformAssertions();

        $this->configureMocks();
        $this->gamePlayValidationServiceImplementation->validateDisconnectionApplicable($this->gamePlay, $this->player);
    }

    public function testValidateDisconnectionApplicableExceptionNotPlayer(): void
    {
        $this->expectException(AccessDeniedHttpException::class);

        $this->configureMocks(false);
        $this->gamePlayValidationServiceImplementation->validateDisconnectionApplicable($this->gamePlay, $this->player);
    }

    public function testValidateDisconnectionApplicableExceptionFinished(): void
    {
        $this->expectException(GamePlayValidationServiceException::class);
        $this->expectExceptionMessage(GamePlayValidationServiceException::MESSAGE_FINISHED);

        $this->configureMocks(true, true);
        $this->gamePlayValidationServiceImplementation->validateDisconnectionApplicable($this->gamePlay, $this->player);
    }

    public function testValidateDisconnectionApplicableExceptionNotPlayerAndFinished(): void
    {
        $this->expectException(AccessDeniedHttpException::class);

        $this->configureMocks(false, true);
        $this->gamePlayValidationServiceImplementation->validateDisconnectionApplicable($this->gamePlay, $this->player);
    }
}
