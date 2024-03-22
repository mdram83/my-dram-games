<?php

namespace App\Http\Controllers\GameCore;

use App\Events\GameCore\GamePlay\GamePlayStartedEvent;
use App\GameCore\GameInvite\GameInviteException;
use App\GameCore\GameInvite\GameInviteRepository;
use App\GameCore\GamePlay\GamePlayAbsFactoryRepository;
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

            GamePlayStartedEvent::dispatch($gameInvite, $gamePlay);

            return new Response([], 200);

        } catch (GameInviteException $e) {
            DB::rollBack();
            throw new HttpException(SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());

        } catch (Exception) {
            DB::rollBack();
            throw new HttpException(SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, static::MESSAGE_INTERNAL_ERROR);
        }
    }

    public function show(): Response
    {
        // TODO temp, needs validation, auth, player etc, GamePlay object created etc.
        return new Response('temp response', 200);
    }
}
