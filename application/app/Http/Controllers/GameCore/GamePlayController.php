<?php

namespace App\Http\Controllers\GameCore;

use App\Events\GameCore\GamePlay\GamePlayStoredEvent;
use App\GameCore\GameElements\GameMove\GameMoveAbsFactoryRepository;
use App\GameCore\GameInvite\GameInviteException;
use App\GameCore\GameInvite\GameInviteRepository;
use App\GameCore\GamePlay\GamePlayAbsFactoryRepository;
use App\GameCore\GamePlay\GamePlayRepository;
use App\GameCore\GamePlayStorage\GamePlayStorageException;
use App\GameCore\Player\Player;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GamePlayController extends Controller
{
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
        }
    }

    public function move(Player $player, Request $request, GameMoveAbsFactoryRepository $moveFactoryRepository): Response
    {
        return new Response([], 200);

        // 3. LATER (for Moves) I need dedicated Events
        // 4. LATER (for Moves) I need dedicated GamePlay Move handling
    }
}
