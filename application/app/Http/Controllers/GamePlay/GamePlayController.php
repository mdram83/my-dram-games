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
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GamePlayController extends Controller
{
    public function join(Request $request, GameRepository $repository, int|string $gameId): View|Response|RedirectResponse
    {
        try {
            $game = $repository->getOne($gameId);
            $player = $request->user();

            if (!$game->isPlayerAdded($player)) {
                return new Response('Forbidden', SymfonyResponse::HTTP_FORBIDDEN);
            }

            if ($game->isHost($player)) {
                GamePlayStartedEvent::dispatch($game);
            }

        } catch (GameException $e) {
            return Redirect::route('home')->withErrors(['general' => $e->getMessage()]);

        }
        catch (Exception) {
            throw new HttpException(SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, 'Internal error');
        }

        // TODO update later with proper GamePlay object created etc.
        return new Response('temp response', 200);
    }
}
