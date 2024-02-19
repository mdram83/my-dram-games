<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllerException;
use App\Models\GameCore\Game\GameException;
use App\Models\GameCore\Game\GameFactory;
use App\Models\GameCore\Game\GameRepository;
use App\Models\GameCore\GameDefinition\GameDefinitionException;
use App\Models\GameCore\Player\Player;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GameController extends Controller
{
    public const MESSAGE_PLAYER_JOINED = 'You have joined the game!';
    public const MESSAGE_PLAYER_BACK = 'Welcome back!';
    public const MESSAGE_INCORRECT_INPUTS = 'Incorrect inputs';

    public function store(Request $request, GameFactory $factory, Player $player): Response
    {
        try {
            $this->validateStoreRequest($request);
            $game = $factory->create(
                $request->input('slug'),
                $request->input('numberOfPlayers'),
                $player
            );
            $responseContent = ['game' => $game->toArray()];

        } catch (ControllerException|GameDefinitionException|GameException $e) {
            return new Response(['message' => $e->getMessage()], SymfonyResponse::HTTP_BAD_REQUEST);

        } catch (Exception) {
            return new Response(['message' => static::MESSAGE_INTERNAL_ERROR], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response($responseContent, SymfonyResponse::HTTP_OK);
    }

    public function join(GameRepository $repository, Player $player, string $slug, int|string $gameId): View|Response|RedirectResponse
    {
        try {
            $game = $repository->getOne($gameId);

            if (!$game->isPlayerAdded($player)) {
                $game->addPlayer($player);
                $message = static::MESSAGE_PLAYER_JOINED;
            }

            $responseContent = [
                'gameDefinition' => $game->getGameDefinition()->toArray(),
                'game' => $game->toArray(),
            ];

        } catch (GameException $e) {
            return Redirect::route('games.show', ['slug' => $slug])->withErrors(['general' => $e->getMessage()]);

        } catch (Exception) {
            throw new HttpException(SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, static::MESSAGE_INTERNAL_ERROR);
        }

        Session::flash('success', ($message ?? static::MESSAGE_PLAYER_BACK));
        return view('single', $responseContent);
    }

    private function validateStoreRequest(Request $request): void
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required|string|max:255',
            'numberOfPlayers' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            $message = json_encode(['message' => static::MESSAGE_INCORRECT_INPUTS, 'errors' => $validator->errors()]);
            throw new ControllerException($message);
        }
    }
}
