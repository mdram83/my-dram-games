<?php

namespace App\Http\Controllers\GameCore;

use App\Events\GamePlay\GamePlayStoredEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllerException;
use App\Http\Controllers\Traits\DispatchGamePlayMovedEventTrait;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use MyDramGames\Core\Exceptions\GameBoxException;
use MyDramGames\Core\Exceptions\GameInviteException;
use MyDramGames\Core\Exceptions\GameMoveException;
use MyDramGames\Core\Exceptions\GamePlayException;
use MyDramGames\Core\Exceptions\GamePlayStorageException;
use MyDramGames\Core\GameInvite\GameInviteRepository;
use MyDramGames\Core\GameMove\GameMove;
use MyDramGames\Core\GamePlay\GamePlay;
use MyDramGames\Core\GamePlay\GamePlayFactory;
use MyDramGames\Core\GamePlay\GamePlayRepository;
use MyDramGames\Utils\Exceptions\GameBoardException;
use MyDramGames\Utils\Player\Player;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GamePlayController extends Controller
{
    use DispatchGamePlayMovedEventTrait;

    public const string MESSAGE_INCORRECT_INPUTS = 'Incorrect inputs';
    public const string MESSAGE_FINISHED = 'Gameplay already finished';

    public function __construct(
        readonly private GamePlayRepository $gamePlayRepository,
        readonly private GameInviteRepository $gameInviteRepository,
        readonly private GamePlayFactory $gamePlayFactory,
    )
    {

    }

    public function store(Player $player, Request $request): View|Response|RedirectResponse
    {
        try {
            DB::beginTransaction();

            $gameInvite = $this->gameInviteRepository->getOne($request->input('gameInviteId'));

            if (!$gameInvite->isPlayer($player) || !$gameInvite->isHost($player)) {
                DB::rollBack();
                return new Response(static::MESSAGE_FORBIDDEN, SymfonyResponse::HTTP_FORBIDDEN);
            }

            $gamePlay = $this->gamePlayFactory->create($gameInvite);

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

    public function show(Player $player, int|string $gamePlayId): Response|View|RedirectResponse
    {
        try {

            $gamePlay = $this->gamePlayRepository->getOne($gamePlayId);

            if (!$gamePlay->getPlayers()->exist($player->getId())) {
                throw new AccessDeniedHttpException(static::MESSAGE_FORBIDDEN);
            }

            if ($gamePlay->isFinished()) {
                return Redirect::route('game-invites.join', [
                    'slug' => $gamePlay->getGameInvite()->getGameBox()->getSlug(),
                    'gameInviteId' => $gamePlay->getGameInvite()->getId(),
                ]);
            }

            $options = array_map(
                fn($item) => $item->getConfiguredValue(),
                $gamePlay->getGameInvite()->getGameSetup()->getAllOptions()->toArray()
            );

            return view('play', [
                'gamePlayId' => $gamePlayId,
                'gameInvite' => [
                    'gameInviteId' => $gamePlay->getGameInvite()->getId(),
                    'slug' => $gamePlay->getGameInvite()->getGameBox()->getSlug(),
                    'name' => $gamePlay->getGameInvite()->getGameBox()->getName(),
                    'host' => $gamePlay->getGameInvite()->getHost()->getName(),
                    'options' => $options,
                ],
                'situation' => $gamePlay->getSituation($player)
            ]);

        } catch (AccessDeniedHttpException $e) {
            return response()->view('errors.403', ['exception' => $e], 403);

        } catch (GamePlayStorageException $e) {
            throw new NotFoundHttpException(static::MESSAGE_NOT_FOUND);

        } catch (Exception $e) {
            throw new HttpException(SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, static::MESSAGE_INTERNAL_ERROR);
        }
    }

    public function move(Player $player, Request $request, int|string $gamePlayId): Response
    {
        try {

            DB::beginTransaction();

            $gamePlay = $this->gamePlayRepository->getOne($gamePlayId);

            if (!$gamePlay->getPlayers()->exist($player->getId())) {
                DB::rollBack();
                return new Response(static::MESSAGE_FORBIDDEN, SymfonyResponse::HTTP_FORBIDDEN);
            }

            $gamePlay->handleMove($this->getMove($player, $gamePlay, $this->getValidatedMoveInputs($request)));
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
    private function getValidatedMoveInputs(Request $request): array
    {
        $validator = Validator::make($request->all(), ['move' => 'required|array']);

        if ($validator->fails()) {
            $message = json_encode(['message' => static::MESSAGE_INCORRECT_INPUTS, 'errors' => $validator->errors()]);
            throw new ControllerException($message);
        }

        return $validator->validated()['move'];
    }

    /**
     * @throws GameBoxException
     */
    private function getMove(Player $player, GamePlay $gamePlay, array $inputs): GameMove
    {
        return ($gamePlay->getGameInvite()->getGameBox()->getGameMoveFactoryClassname())::create($player, $inputs);
    }
}
