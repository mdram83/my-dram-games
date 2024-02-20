<?php

namespace App\Http\Controllers\GameCore;

use App\Events\GameCore\GamePlay\GamePlayStartedEvent;
use App\GameCore\GameInvite\GameInviteException;
use App\GameCore\GameInvite\GameInviteRepository;
use App\GameCore\Player\Player;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GamePlayController extends Controller
{
    public function store(Request $request, GameInviteRepository $repository, Player $player): View|Response|RedirectResponse
    {
        try {
            $game = $repository->getOne($request->input('gameId'));

            if (!$game->isPlayerAdded($player) || !$game->isHost($player)) {
                return new Response(static::MESSAGE_FORBIDDEN, SymfonyResponse::HTTP_FORBIDDEN);
            }

            // TODO here I will need to create proper GamePlay object
            GamePlayStartedEvent::dispatch($game);

        } catch (GameInviteException $e) {
            throw new HttpException(SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());

        }
        catch (Exception) {
            throw new HttpException(SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, static::MESSAGE_INTERNAL_ERROR);
        }

        // TODO update later with proper GamePlay object created etc. if needed
        return new Response([], 200);
    }

    public function show(): Response
    {
        // TODO temp, needs validation, auth, player etc, GamePlay object created etc.
        return new Response('temp response', 200);
    }
}
