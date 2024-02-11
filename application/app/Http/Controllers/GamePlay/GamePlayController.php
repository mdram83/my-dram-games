<?php

namespace App\Http\Controllers\GamePlay;

use App\Events\GameCore\GamePlay\GamePlayStartedEvent;
use App\Http\Controllers\Controller;
use App\Models\GameCore\Game\GameException;
use App\Models\GameCore\Game\GameRepository;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GamePlayController extends Controller
{
    public function store(
        Request $request, GameRepository $repository, int|string $gameId
    ): View|Response|RedirectResponse
    {
        if (!Auth::check()) {
            return new Response('Unauthorized', SymfonyResponse::HTTP_UNAUTHORIZED);
        }

        try {
            $game = $repository->getOne($gameId);
            $player = $request->user();

            if (!$game->isPlayerAdded($player) || !$game->isHost($player)) {
                return new Response('Forbidden', SymfonyResponse::HTTP_FORBIDDEN);
            }

            // TODO here I will need to create proper GamePlay object
            GamePlayStartedEvent::dispatch($game);

        } catch (GameException $e) {
            throw new HttpException(SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());

        }
        catch (Exception) {
            throw new HttpException(SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, 'Internal error');
        }

        // TODO update later with proper GamePlay object created etc. if needed
        return new Response([], 200);
    }

    public function join(): Response
    {
        // TODO temp, needs validation, auth, player etc, GamePlay object created etc.
        return new Response('temp response', 200);
    }
}
