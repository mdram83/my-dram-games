<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllerException;
use App\Models\GameCore\Game\GameEloquent;
use App\Models\GameCore\Game\GameException;
use App\Models\GameCore\Game\GameFactory;
use App\Models\GameCore\Game\GameRepository;
use App\Models\GameCore\GameDefinition\GameDefinitionException;
use App\Models\GameCore\GameDefinition\GameDefinitionFactoryPhpConfig;
use App\Models\GameCore\GameDefinition\GameDefinitionRepository;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GameController extends Controller
{
    public function store(Request $request, GameFactory $factory, string $slug): Response
    {
        try {
            $this->validateStoreRequest($request, $slug);
            $game = $factory->create($slug, $request->input('numberOfPlayers'), $request->user());
            $responseContent = ['game' => $game->toArray()];

        } catch (ControllerException $e) {
            return new Response($e->getMessage(), $e->getCode());

        } catch (GameDefinitionException|GameException $e) {
            return new Response(['message' => $e->getMessage()], SymfonyResponse::HTTP_BAD_REQUEST);

        } catch (Exception) {
            return new Response(['message' => 'Internal error'], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response($responseContent, SymfonyResponse::HTTP_OK);
    }

    public function update(Request $request, GameRepository $repository, string $slug, int|string $gameId): View|Response|RedirectResponse
    {
        try {
            $game = $repository->getOne($gameId);
            $currentPlayer = $request->user();

            if (!in_array($currentPlayer->getId(), array_map(fn($player) => $player->getId(), $game->getPlayers()))) {
                $game->addPlayer($currentPlayer);
                $message = 'You have joined the game!';
            }

            $responseContent = [
                'gameDefinition' => $game->getGameDefinition()->toArray(),
                'game' => $game->toArray(),
            ];

        } catch (GameException $e) {
            return Redirect::route('games', ['slug' => $slug])->withErrors(['general' => $e->getMessage()]);

        } catch (Exception) {
            throw new HttpException(SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, 'Internal error');
        }

        Session::flash('success', ($message ?? 'Welcome back!'));
        return view('single', $responseContent);
    }

    private function validateStoreRequest(Request $request, string $slug): void
    {
        try {
            $this->validateAuth();
            $this->validateStoreRequestInputs($request, $slug);
        } catch (Exception $e) {
            throw new ControllerException($e->getMessage(), $e->getCode());
        }
    }

    private function validateAuth(): void
    {
        if (!Auth::check()) {
            $message = json_encode(['message' => 'Unauthorized request']);
            throw new class($message, SymfonyResponse::HTTP_UNAUTHORIZED) extends Exception {};
        }
    }

    private function validateStoreRequestInputs(Request $request, string $slug): void
    {
        $validator = Validator::make(array_merge($request->all(), ['slug' => $slug]), [
            'slug' => 'required|string|max:255',
            'numberOfPlayers' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            $message = json_encode(['message' => 'Incorrect inputs', 'errors' => $validator->errors()]);
            throw new class($message, SymfonyResponse::HTTP_BAD_REQUEST) extends Exception {};
        }
    }
}
