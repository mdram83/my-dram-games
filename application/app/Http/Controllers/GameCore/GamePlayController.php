<?php

namespace App\Http\Controllers\GameCore;

use App\Events\GamePlay\GamePlayStoredEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DispatchGamePlayMovedEventTrait;
use App\Http\Requests\GameCore\GameMoveRequest;
use App\Services\GamePlay\GamePlayValidationService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use MyDramGames\Core\Exceptions\GameBoxException;
use MyDramGames\Core\Exceptions\GamePlayException;
use MyDramGames\Core\Exceptions\GamePlayStorageException;
use MyDramGames\Core\GameInvite\GameInviteRepository;
use MyDramGames\Core\GameMove\GameMove;
use MyDramGames\Core\GameMove\GameMoveFactory;
use MyDramGames\Core\GamePlay\GamePlay;
use MyDramGames\Core\GamePlay\GamePlayFactory;
use MyDramGames\Core\GamePlay\GamePlayRepository;
use MyDramGames\Utils\Player\Player;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GamePlayController extends Controller
{
    use DispatchGamePlayMovedEventTrait;

    public function __construct(
        readonly private GamePlayRepository $gamePlayRepository,
        readonly private GameInviteRepository $gameInviteRepository,
        readonly private GamePlayFactory $gamePlayFactory,
        readonly private GamePlayValidationService $gamePlayValidationService,
    )
    {

    }

    /**
     * @throws Exception
     */
    public function store(Player $player, Request $request): View|Response|RedirectResponse
    {
        try {

            [$gameInvite, $gamePlay] = DB::transaction(function () use ($player, $request) {

                $gameInvite = $this->gameInviteRepository->getOne($request->input('gameInviteId'));

                if (!$gameInvite->isPlayer($player) || !$gameInvite->isHost($player)) {
                    throw new AccessDeniedHttpException();
                }

                $gamePlay = $this->gamePlayFactory->create($gameInvite);

                return [$gameInvite, $gamePlay];
            });

            GamePlayStoredEvent::dispatch($gameInvite, $gamePlay);

            return new Response([], 200);

        } catch (GamePlayStorageException|GamePlayException $e) {
            throw new Exception($e->getMessage(), previous: $e);

        }
    }

    /**
     * @throws GameBoxException
     */
    public function show(Player $player, int|string $gamePlayId): Response|View|RedirectResponse
    {
        try {

            $gamePlay = $this->gamePlayRepository->getOne($gamePlayId);

        } catch (GamePlayStorageException $e) {
            throw new NotFoundHttpException($e->getMessage(), previous: $e);

        }

        $this->gamePlayValidationService->validateGamePlayPlayer($gamePlay, $player);

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
    }

    public function move(Player $player, GameMoveRequest $request, int|string $gamePlayId): Response
    {
        $gamePlay = DB::transaction(function () use ($player, $request, $gamePlayId) {
            $gamePlay = $this->gamePlayRepository->getOne($gamePlayId);
            $this->gamePlayValidationService->validateGamePlayPlayer($gamePlay, $player);
            $gamePlay->handleMove($this->getMove($player, $gamePlay, $request->validated('move')));
            return $gamePlay;
        });

        $this->dispatchGamePlayMovedEvent($gamePlay);

        return new Response([], 200);
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
