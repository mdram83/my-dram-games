<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\Models\GameCore\Game\GameEloquent;
use App\Models\GameCore\Game\GameException;
use App\Models\GameCore\GameDefinition\GameDefinitionException;
use App\Models\GameCore\GameDefinition\GameDefinitionFactoryPhpConfig;
use App\Models\GameCore\GameDefinition\GameDefinitionRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class GameController extends Controller
{
    public function store(Request $request, GameDefinitionRepository $repository, string $slug): Response
    {
        try {
            $this->validateAuth();
            $this->validateStoreRequest($request, $slug);

        } catch (Exception $e) {
            return new Response($e->getMessage(), $e->getCode());
        }

        try {
            // TODO streamline below with Factory/Builder/Repo or so
            $gameDefinition = $repository->getOne($slug);
            $game = new GameEloquent(new GameDefinitionFactoryPhpConfig());
            $game->setGameDefinition($gameDefinition);
            $game->setNumberOfPlayers($request->input('numberOfPlayers'));
            $game->addPlayer($request->user(), true);

        } catch (GameDefinitionException|GameException $e) {
            return new Response(['message' => $e->getMessage()], SymfonyResponse::HTTP_BAD_REQUEST);

        } catch (Exception) {
            return new Response(['message' => 'Internal error'], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response([], SymfonyResponse::HTTP_OK);
    }

    private function validateAuth(): void
    {
        if (!Auth::check()) {
            $message = json_encode(['message' => 'Unauthorized request']);
            throw new class($message, SymfonyResponse::HTTP_UNAUTHORIZED) extends Exception {};
        }
    }

    private function validateStoreRequest(Request $request, string $slug): void
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
