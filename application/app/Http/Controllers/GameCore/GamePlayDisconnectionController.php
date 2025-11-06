<?php

namespace App\Http\Controllers\GameCore;

use App\Events\GamePlay\GamePlayDisconnectedEvent;
use App\Http\Controllers\Traits\ValidateGamePlayNotFinishedTrait;
use App\Services\GamePlay\GamePlayValidationService;
use App\Services\GamePlayDisconnection\GamePlayDisconnectionFactory;
use App\Services\GamePlayDisconnection\GamePlayDisconnectionRepository;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllerValidationException;
use App\Http\Controllers\Traits\DispatchGamePlayMovedEventTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use MyDramGames\Core\GameOption\Values\GameOptionValueForfeitAfterGeneric;
use MyDramGames\Core\GamePlay\GamePlay;
use MyDramGames\Core\GamePlay\GamePlayRepository;
use MyDramGames\Utils\Exceptions\CollectionException;
use MyDramGames\Utils\Player\Player;

class GamePlayDisconnectionController extends Controller
{
    use DispatchGamePlayMovedEventTrait;
    use ValidateGamePlayNotFinishedTrait;

    public function __construct(
        readonly private GamePlayRepository $gamePlayRepository,
        readonly private GamePlayDisconnectionRepository $gamePlayDisconnectionRepository,
        readonly private GamePlayDisconnectionFactory $gamePlayDisconnectionFactory,
        readonly private GamePlayValidationService $gamePlayValidationService,
    )
    {

    }

    public function disconnect(Player $player, Request $request, int|string $gamePlayId): Response
    {
        [$gamePlay, $disconnectedPlayer] = DB::transaction(function () use ($player, $request, $gamePlayId) {

            $gamePlay = $this->gamePlayRepository->getOne($gamePlayId);

            $this->gamePlayValidationService->validateGamePlayPlayer($gamePlay, $player);
            $this->validateGamePlayNotFinished($gamePlay);

            $disconnectedPlayer = $this->getValidatedDisconnectedPlayer($request, $gamePlay);
            $disconnection = $this->gamePlayDisconnectionRepository->getOneByGamePlayAndPlayer($gamePlay, $disconnectedPlayer);

            if ($disconnection === null) {
                $this->gamePlayDisconnectionFactory->create($gamePlay, $disconnectedPlayer);
            } else {
                $disconnection->setDisconnectedAt();
                $disconnection->save();
            }

            return [$gamePlay, $disconnectedPlayer];
        });

        GamePlayDisconnectedEvent::dispatch($gamePlay, $disconnectedPlayer);

        return new Response([], 200);
    }

    public function connect(Player $player, int|string $gamePlayId): Response
    {
        DB::transaction(function () use ($player, $gamePlayId) {

            $gamePlay = $this->gamePlayRepository->getOne($gamePlayId);

            $this->gamePlayValidationService->validateGamePlayPlayer($gamePlay, $player);
            $this->validateGamePlayNotFinished($gamePlay);

            $this->gamePlayDisconnectionRepository->getOneByGamePlayAndPlayer($gamePlay, $player)?->remove();
        });

        return new Response([], 200);
    }

    public function forfeitAfterDisconnection(Player $player, Request $request, int|string $gamePlayId): Response
    {
        $gamePlay = DB::transaction(function () use ($player, $request, $gamePlayId) {

            $gamePlay = $this->gamePlayRepository->getOne($gamePlayId);

            $this->gamePlayValidationService->validateGamePlayPlayer($gamePlay, $player);
            $this->validateGamePlayNotFinished($gamePlay);

            $forfeitAfterOptionValue = $gamePlay
                ->getGameInvite()
                ->getGameSetup()
                ->getOption('forfeitAfter')
                ->getConfiguredValue();

            if ($forfeitAfterOptionValue === GameOptionValueForfeitAfterGeneric::Disabled) {
                throw new ControllerValidationException(ControllerValidationException::MESSAGE_FORFEIT_AFTER_DISABLED);
            }

            $disconnectedPlayer = $this->getValidatedDisconnectedPlayer($request, $gamePlay);
            $disconnection = $this->gamePlayDisconnectionRepository->getOneByGamePlayAndPlayer($gamePlay, $disconnectedPlayer);

            if ($disconnection === null) {
                throw new ControllerValidationException(ControllerValidationException::MESSAGE_FORFEIT_AFTER_EARLY);
            }

            if (!$disconnection->hasExpired($forfeitAfterOptionValue->getValue())) {
                throw new ControllerValidationException(ControllerValidationException::MESSAGE_FORFEIT_AFTER_EARLY);
            }

            $gamePlay->handleForfeit($disconnectedPlayer);

            return $gamePlay;
        });

        $this->dispatchGamePlayMovedEvent($gamePlay);

        return new Response([], 200);
    }

    /**
     * @throws ControllerValidationException|CollectionException
     */
    private function getValidatedDisconnectedPlayer(Request $request, GamePlay $gamePlay): Player
    {
        $singlePlayerCollection = $gamePlay
            ->getPlayers()
            ->filter(fn($item) => $item->getName() === $request->get('disconnected'));

        if ($singlePlayerCollection->count() === 0) {
            throw new ControllerValidationException(ControllerValidationException::MESSAGE_INCORRECT_INPUTS);
        }

        return $singlePlayerCollection->pullFirst();
    }
}
