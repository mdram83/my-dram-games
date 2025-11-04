<?php

namespace App\Http\Controllers\GameCore;

use App\Events\GamePlay\GamePlayStoredEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllerException;
use App\Http\Controllers\Traits\DispatchGamePlayMovedEventTrait;
use App\Http\Controllers\Traits\ValidateGamePlayPlayerTrait;
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
use MyDramGames\Core\Exceptions\GameMoveException;
use MyDramGames\Core\Exceptions\GamePlayException;
use MyDramGames\Core\Exceptions\GamePlayStorageException;
use MyDramGames\Core\GameInvite\GameInviteRepository;
use MyDramGames\Core\GameMove\GameMove;
use MyDramGames\Core\GameMove\GameMoveFactory;
use MyDramGames\Core\GamePlay\GamePlay;
use MyDramGames\Core\GamePlay\GamePlayFactory;
use MyDramGames\Core\GamePlay\GamePlayRepository;
use MyDramGames\Utils\Exceptions\GameBoardException;
use MyDramGames\Utils\Player\Player;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class GamePlayController extends Controller
{
    use DispatchGamePlayMovedEventTrait;
    use ValidateGamePlayPlayerTrait;

    public const string MESSAGE_INCORRECT_INPUTS = 'Incorrect inputs';
    public const string MESSAGE_FINISHED = 'Gameplay already finished';

    public function __construct(
        readonly private GamePlayRepository $gamePlayRepository,
        readonly private GameInviteRepository $gameInviteRepository,
        readonly private GamePlayFactory $gamePlayFactory,
    )
    {

    }

    /**
     * @throws Exception
     */
    public function store(Player $player, Request $request): View|Response|RedirectResponse
    {
        try {
            DB::beginTransaction();

            $gameInvite = $this->gameInviteRepository->getOne($request->input('gameInviteId'));

            if (!$gameInvite->isPlayer($player) || !$gameInvite->isHost($player)) {
                throw new AccessDeniedHttpException();
            }

            $gamePlay = $this->gamePlayFactory->create($gameInvite);

            DB::commit();

            GamePlayStoredEvent::dispatch($gameInvite, $gamePlay);

            return new Response([], 200);

        } catch (GamePlayStorageException $e) {
            throw new Exception($e->getMessage(), previous: $e);

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws GameBoxException
     */
    public function show(Player $player, int|string $gamePlayId): Response|View|RedirectResponse
    {
        try {

            $gamePlay = $this->gamePlayRepository->getOne($gamePlayId);

            $this->validateGamePlayPlayer($gamePlay, $player);

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

        }
    }

    /**
     * @throws GameBoxException
     * @throws ControllerException
     * @throws ValidationException
     */
    public function move(Player $player, Request $request, int|string $gamePlayId): Response
    {
        try {

            DB::beginTransaction();

            $gamePlay = $this->gamePlayRepository->getOne($gamePlayId);

            $this->validateGamePlayPlayer($gamePlay, $player);

            $gamePlay->handleMove($this->getMove($player, $gamePlay, $this->getValidatedMoveInputs($request)));
            $this->dispatchGamePlayMovedEvent($gamePlay);

            DB::commit();

            return new Response([], 200);

        } catch (ControllerException|GameMoveException|GameBoardException|GamePlayException $e) {
            DB::rollBack();
            return new Response(['message' => $e->getMessage()], SymfonyResponse::HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            DB::rollBack();
            throw  $e;
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
        /** @var GameMoveFactory $className */
        $className = $gamePlay->getGameInvite()->getGameBox()->getGameMoveFactoryClassname();
        return $className::create($player, $inputs);
    }
}
