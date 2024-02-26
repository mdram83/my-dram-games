<?php

namespace App\Http\Controllers\GameCore;

use App\GameCore\GameInvite\GameInviteException;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameInvite\GameInviteRepository;
use App\GameCore\GameBox\GameBoxException;
use App\GameCore\GameSetup\GameSetupException;
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
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GameInviteController extends Controller
{
    public const MESSAGE_PLAYER_JOINED = 'You have joined the game!';
    public const MESSAGE_PLAYER_BACK = 'Welcome back!';
    public const MESSAGE_INCORRECT_INPUTS = 'Incorrect inputs';

    public function store(Request $request, GameInviteFactory $factory, Player $player): Response
    {
        try {
            $inputs = $this->getValidatedCastedStoreInputs($request);

            DB::beginTransaction();;
            $gameInvite = $factory->create($inputs['slug'], $inputs['options'], $player);
            DB::commit();

            $responseContent = ['gameInvite' => $gameInvite->toArray()];
            return new Response($responseContent, SymfonyResponse::HTTP_OK);

        } catch (ControllerException $e) {
            return new Response(['message' => $e->getMessage()], SymfonyResponse::HTTP_BAD_REQUEST);

        }  catch (GameSetupException|GameBoxException|GameInviteException $e) {
            DB::rollBack();
            return new Response(['message' => $e->getMessage()], SymfonyResponse::HTTP_BAD_REQUEST);

        } catch (Exception) {
            DB::rollBack();
            return new Response(['message' => static::MESSAGE_INTERNAL_ERROR], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function join(GameInviteRepository $repository, Player $player, string $slug, int|string $gameInviteId): View|Response|RedirectResponse
    {
        try {
            $gameInvite = $repository->getOne($gameInviteId);

            if (!$gameInvite->isPlayerAdded($player)) {
                $gameInvite->addPlayer($player);
                $message = static::MESSAGE_PLAYER_JOINED;
            }

            $responseContent = [
                'gameBox' => $gameInvite->getGameBox()->toArray(),
                'gameInvite' => $gameInvite->toArray(),
            ];

        } catch (GameInviteException $e) {
            return Redirect::route('games.show', ['slug' => $slug])->withErrors(['general' => $e->getMessage()]);

        } catch (Exception) {
            throw new HttpException(SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, static::MESSAGE_INTERNAL_ERROR);
        }

        Session::flash('success', ($message ?? static::MESSAGE_PLAYER_BACK));
        return view('single', $responseContent);
    }

    /**
     * @throws ControllerException|ValidationException
     */
    private function getValidatedCastedStoreInputs(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required|string|max:255',
            'options.numberOfPlayers' => 'required|integer|min:1',
            'options.autostart' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            $message = json_encode(['message' => static::MESSAGE_INCORRECT_INPUTS, 'errors' => $validator->errors()]);
            throw new ControllerException($message);
        }

        $inputs = $validator->validated();
        $inputs['options']['numberOfPlayers'] = (int) $inputs['options']['numberOfPlayers'];
        $inputs['options']['autostart'] = (bool) $inputs['options']['autostart'];

        return $inputs;
    }
}
