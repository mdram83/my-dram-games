<?php

namespace App\Http\Controllers\GameCore;

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
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GamePlayController extends Controller
{
    public const MESSAGE_INCORRECT_INPUTS = 'Incorrect inputs';

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

    public function show(GamePlayRepository $repository, Player $player, int|string $gamePlayId): Response|View
    {
        try {

            $gamePlay = $repository->getOne($gamePlayId);

            if (!$gamePlay->getPlayers()->exist($player->getId())) {
                return new Response(static::MESSAGE_FORBIDDEN, SymfonyResponse::HTTP_FORBIDDEN);
            }

            return view('play', [
                'gamePlayId' => $gamePlayId,
                'gameInvite' => [
                    'gameInviteId' => $gamePlay->getGameInvite()->getId(),
                    'slug' => $gamePlay->getGameInvite()->getGameBox()->getSlug(),
                    'name' => $gamePlay->getGameInvite()->getGameBox()->getName(),
                    'host' => $gamePlay->getGameInvite()->getHost()->getName(),
                ],
                'situation' => $gamePlay->getSituation($player)],
            );

        } catch (GamePlayStorageException $e) {
            return new Response(static::MESSAGE_NOT_FOUND, SymfonyResponse::HTTP_NOT_FOUND);

        } catch (Exception) {
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

            $validatedInputs = $this->getValidatedInputs($request);
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

    /**
     * @throws ControllerException
     * @throws ValidationException
     */
    private function getValidatedInputs(Request $request): array
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

    private function dispatchGamePlayMovedEvent(GamePlay $gamePlay): void
    {
        foreach ($gamePlay->getGameInvite()->getPlayers() as $player) {
            GamePlayMovedEvent::dispatch($gamePlay, $player);
        }
    }
}
