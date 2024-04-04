<?php

namespace App\Http\Controllers\GameCore;

use App\Events\GameCore\GamePlay\GamePlayDisconnectedEvent;
use App\Events\GameCore\GamePlay\GamePlayMovedEvent;
use App\Events\GameCore\GamePlay\GamePlayStoredEvent;
use App\GameCore\GameElements\GameBoard\GameBoardException;
use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameElements\GameMove\GameMoveAbsFactoryRepository;
use App\GameCore\GameElements\GameMove\GameMoveException;
use App\GameCore\GameInvite\GameInviteException;
use App\GameCore\GameInvite\GameInviteRepository;
use App\GameCore\GamePlay\GamePlay;
use App\GameCore\GamePlay\GamePlayAbsFactoryRepository;
use App\GameCore\GamePlay\GamePlayException;
use App\GameCore\GamePlay\GamePlayRepository;
use App\GameCore\GamePlayDisconnection\GamePlayDisconnectException;
use App\GameCore\GamePlayDisconnection\GamePlayDisconnectionFactory;
use App\GameCore\GamePlayDisconnection\GamePlayDisconnectionRepository;
use App\GameCore\GamePlayStorage\GamePlayStorageException;
use App\GameCore\Player\Player;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllerException;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GamePlayController extends Controller
{
    public const MESSAGE_INCORRECT_INPUTS = 'Incorrect inputs';
    public const MESSAGE_FINISHED = 'Gameplay already finished';

    public function store(
        Request $request,
        GameInviteRepository $repository,
        Player $player,
        GamePlayAbsFactoryRepository $gamePlayAbsFactoryRepository
    ): View|Response|RedirectResponse
    {
        try {
            $gameInvite = $repository->getOne($request->input('gameInviteId'));

            if (!$gameInvite->isPlayerAdded($player) || !$gameInvite->isHost($player)) {
                return new Response(static::MESSAGE_FORBIDDEN, SymfonyResponse::HTTP_FORBIDDEN);
            }

            $factory = $gamePlayAbsFactoryRepository->getOne($gameInvite->getGameBox()->getSlug());

            DB::beginTransaction();
            $gamePlay = $factory->create($gameInvite);
            DB::commit();

            GamePlayStoredEvent::dispatch($gameInvite, $gamePlay);

            return new Response([], 200);

        } catch (GameInviteException $e) {
            DB::rollBack();
            throw new HttpException(SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());

        } catch (Exception) {
            DB::rollBack();
            throw new HttpException(SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, static::MESSAGE_INTERNAL_ERROR);
        }
    }

    public function show(GamePlayRepository $repository, Player $player, int|string $gamePlayId): Response|View|RedirectResponse
    {
        try {
            $gamePlay = $repository->getOne($gamePlayId);

            if (!$gamePlay->getPlayers()->exist($player->getId())) {
                throw new AccessDeniedHttpException(static::MESSAGE_FORBIDDEN);
            }

            if ($gamePlay->isFinished()) {

                return Redirect::route('game-invites.join', [
                    'slug' => $gamePlay->getGameInvite()->getGameBox()->getSlug(),
                    'gameInviteId' => $gamePlay->getGameInvite()->getId(),
                ]);
            }

            return view(
                'play',
                [
                    'gamePlayId' => $gamePlayId,
                    'gameInvite' => [
                        'gameInviteId' => $gamePlay->getGameInvite()->getId(),
                        'slug' => $gamePlay->getGameInvite()->getGameBox()->getSlug(),
                        'name' => $gamePlay->getGameInvite()->getGameBox()->getName(),
                        'host' => $gamePlay->getGameInvite()->getHost()->getName(),
                    ],
                    'situation' => $gamePlay->getSituation($player)
                ],
            );

        } catch (AccessDeniedHttpException $e) {
            return response()->view('errors.403', ['exception' => $e], 403);

        } catch (GamePlayStorageException $e) {
            throw new NotFoundHttpException(static::MESSAGE_NOT_FOUND);

        } catch (Exception $e) {
            throw new HttpException(SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, static::MESSAGE_INTERNAL_ERROR);
        }
    }

    public function move(
        Player $player,
        Request $request,
        GameMoveAbsFactoryRepository $gameMoveAbsFactoryRepository,
        GamePlayRepository $gamePlayRepository,
        int|string $gamePlayId
    ): Response
    {
        try {

            DB::beginTransaction();

            $gamePlay = $gamePlayRepository->getOne($gamePlayId);

            if (!$gamePlay->getPlayers()->exist($player->getId())) {
                DB::rollBack();
                return new Response(static::MESSAGE_FORBIDDEN, SymfonyResponse::HTTP_FORBIDDEN);
            }

            $validatedInputs = $this->getValidatedMoveInputs($request);
            $move = $this->getMove($player, $gamePlay, $gameMoveAbsFactoryRepository, $validatedInputs);

            $gamePlay->handleMove($move);

            $this->dispatchGamePlayMovedEvent($gamePlay);

            DB::commit();

            return new Response([], 200);

        } catch (GamePlayStorageException $e) {
            DB::rollBack();
            return new Response(static::MESSAGE_NOT_FOUND, SymfonyResponse::HTTP_NOT_FOUND);

        } catch (ControllerException|GameMoveException|GameBoardException|GamePlayException $e) {
            DB::rollBack();
            return new Response(['message' => $e->getMessage()], SymfonyResponse::HTTP_BAD_REQUEST);

        } catch (Exception) {
            DB::rollBack();
            throw new HttpException(SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, static::MESSAGE_INTERNAL_ERROR);
        }
    }

    public function disconnect(
        Player $player,
        Request $request,
        GamePlayRepository $gamePlayRepository,
        GamePlayDisconnectionRepository $disconnectionRepository,
        GamePlayDisconnectionFactory $disconnectionFactory,
        int|string $gamePlayId
    ): Response
    {
        try {
            DB::beginTransaction();

            $gamePlay = $gamePlayRepository->getOne($gamePlayId);

            if (!$gamePlay->getPlayers()->exist($player->getId())) {
                DB::rollBack();
                return new Response(static::MESSAGE_FORBIDDEN, SymfonyResponse::HTTP_FORBIDDEN);
            }

            if ($gamePlay->isFinished()) {
                DB::rollBack();
                return new Response(static::MESSAGE_FINISHED, SymfonyResponse::HTTP_BAD_REQUEST);
            }

            $disconnectedPlayer = $this->getValidatedDisconnectPlayer($request, $gamePlay);

            if (!$disconnection = $disconnectionRepository->getOneByGamePlayAndPlayer($gamePlay, $disconnectedPlayer)) {
                $disconnectionFactory->create($gamePlay, $disconnectedPlayer);
            } else {
                $disconnection->setDisconnectedAt();
                $disconnection->save();
            }

            GamePlayDisconnectedEvent::dispatch($gamePlay, $disconnectedPlayer);

            DB::commit();

            return new Response([], 200);

        } catch (GamePlayStorageException|GamePlayDisconnectException) {
            DB::rollBack();
            return new Response(static::MESSAGE_NOT_FOUND, SymfonyResponse::HTTP_NOT_FOUND);

        } catch (ControllerException $e) {
            DB::rollBack();
            return new Response(['message' => $e->getMessage()], SymfonyResponse::HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpException(SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, static::MESSAGE_INTERNAL_ERROR);
        }
    }

    public function connect(
        Player $player,
        GamePlayRepository $gamePlayRepository,
        GamePlayDisconnectionRepository $disconnectionRepository,
        int|string $gamePlayId
    ): Response
    {
        try {

            DB::beginTransaction();

            $gamePlay = $gamePlayRepository->getOne($gamePlayId);

            if (!$gamePlay->getPlayers()->exist($player->getId())) {
                DB::rollBack();
                return new Response(static::MESSAGE_FORBIDDEN, SymfonyResponse::HTTP_FORBIDDEN);
            }

            if ($gamePlay->isFinished()) {
                DB::rollBack();
                return new Response(static::MESSAGE_FINISHED, SymfonyResponse::HTTP_BAD_REQUEST);
            }

            $disconnectionRepository->getOneByGamePlayAndPlayer($gamePlay, $player)?->remove();

            DB::commit();
            return new Response([], 200);

        } catch (GamePlayStorageException) {
            return new Response(static::MESSAGE_NOT_FOUND, SymfonyResponse::HTTP_NOT_FOUND);

        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpException(SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, static::MESSAGE_INTERNAL_ERROR);
        }
    }

    /**
     * @throws ControllerException
     * @throws ValidationException
     */
    private function getValidatedMoveInputs(Request $request): array
    {
        $validator = Validator::make($request->all(), ['move' => 'required|array']);

        if ($validator->fails()) {
            $message = json_encode(['message' => static::MESSAGE_INCORRECT_INPUTS, 'errors' => $validator->errors()]);
            throw new ControllerException($message);
        }

        return $validator->validated()['move'];
    }

    private function getMove(
        Player $player,
        GamePlay $gamePlay,
        GameMoveAbsFactoryRepository $repository,
        array $inputs
    ): GameMove
    {
        return $repository
            ->getOne($gamePlay->getGameInvite()->getGameBox()->getSlug())
            ->create($player, $inputs);
    }

    /**
     * @throws ControllerException
     */
    private function getValidatedDisconnectPlayer(Request $request, GamePlay $gamePlay): Player
    {
        $singlePlayerCollection = $gamePlay
            ->getPlayers()
            ->filter(fn($item, $key) => $item->getName() === $request->get('disconnected'));

        if ($singlePlayerCollection->count() === 0) {
            throw new ControllerException(static::MESSAGE_INCORRECT_INPUTS);
        }

        return $singlePlayerCollection->pullFirst();
    }

    private function dispatchGamePlayMovedEvent(GamePlay $gamePlay): void
    {
        foreach ($gamePlay->getGameInvite()->getPlayers() as $player) {
            GamePlayMovedEvent::dispatch($gamePlay, $player);
        }
    }
}
