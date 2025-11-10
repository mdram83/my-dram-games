<?php

namespace App\Http\Controllers\GameCore;

use App\Events\GamePlay\GamePlayDisconnectedEvent;
use App\Http\Requests\GameCore\GamePlayDisconnectionRequest;
use App\Services\GamePlay\GamePlayService;
use App\Services\GamePlay\GamePlayValidationService;
use App\Services\GamePlayDisconnection\GamePlayDisconnectionFactory;
use App\Services\GamePlayDisconnection\GamePlayDisconnectionRepository;
use App\Http\Controllers\Controller;
use App\Services\GamePlayDisconnection\GamePlayDisconnectionService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use MyDramGames\Core\GamePlay\GamePlayRepository;
use MyDramGames\Utils\Player\Player;

class GamePlayDisconnectionController extends Controller
{
    public function __construct(
        readonly private GamePlayRepository $gamePlayRepository,
        readonly private GamePlayDisconnectionRepository $gamePlayDisconnectionRepository,
        readonly private GamePlayDisconnectionFactory $gamePlayDisconnectionFactory,
        readonly private GamePlayValidationService $gamePlayValidationService,
        readonly private GamePlayDisconnectionService $gamePlayDisconnectionService,
    )
    {

    }

    public function disconnect(Player $player, GamePlayDisconnectionRequest $request, int|string $gamePlayId): Response
    {
        [$gamePlay, $disconnectedPlayer] = DB::transaction(function () use ($player, $request, $gamePlayId) {

            $gamePlay = $this->gamePlayRepository->getOne($gamePlayId);

            $this->gamePlayValidationService->validateDisconnectionApplicable($gamePlay, $player);

            $disconnectedPlayer = $this->gamePlayDisconnectionService->getValidatedDisconnectedPlayer(
                $request->getDisconnectedPlayerName(),
                $gamePlay
            );

            $this->gamePlayDisconnectionService->setDisconnection(
                $this->gamePlayDisconnectionRepository,
                $this->gamePlayDisconnectionFactory,
                $disconnectedPlayer,
                $gamePlay
            );

            return [$gamePlay, $disconnectedPlayer];
        });

        GamePlayDisconnectedEvent::dispatch($gamePlay, $disconnectedPlayer);

        return new Response([], 200);
    }

    public function connect(Player $player, int|string $gamePlayId): Response
    {
        DB::transaction(function () use ($player, $gamePlayId) {
            $gamePlay = $this->gamePlayRepository->getOne($gamePlayId);
            $this->gamePlayValidationService->validateDisconnectionApplicable($gamePlay, $player);
            $this->gamePlayDisconnectionRepository->getOneByGamePlayAndPlayer($gamePlay, $player)?->remove();
        });

        return new Response([], 200);
    }

    public function forfeitAfterDisconnection(
        Player $player,
        GamePlayDisconnectionRequest $request,
        GamePlayService $gamePlayService,
        int|string $gamePlayId
    ): Response
    {
        $gamePlay = DB::transaction(function () use ($player, $request, $gamePlayId) {

            $gamePlay = $this->gamePlayRepository->getOne($gamePlayId);

            $this->gamePlayValidationService->validateDisconnectionApplicable($gamePlay, $player);

            $disconnectedPlayer = $this->gamePlayDisconnectionService->getValidatedDisconnectedPlayer(
                $request->getDisconnectedPlayerName(),
                $gamePlay
            );

            $this->gamePlayDisconnectionService->validateForfeitAfterApplicable(
                $this->gamePlayDisconnectionRepository,
                $gamePlay,
                $disconnectedPlayer
            );

            $gamePlay->handleForfeit($disconnectedPlayer);

            return $gamePlay;
        });

        $gamePlayService->dispatchGamePlayMovedEventToAllPlayers($gamePlay);

        return new Response([], 200);
    }
}
