<?php

namespace App\Http\Controllers\GameCore;

use App\Events\GamePlay\GamePlayDisconnectedEvent;
use App\Services\GamePlayDisconnection\GamePlayDisconnectException;
use App\Services\GamePlayDisconnection\GamePlayDisconnectionFactory;
use App\Services\GamePlayDisconnection\GamePlayDisconnectionRepository;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllerException;
use App\Http\Controllers\Traits\DispatchGamePlayMovedEventTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use MyDramGames\Core\Exceptions\GamePlayStorageException;
use MyDramGames\Core\Exceptions\GameSetupException;
use MyDramGames\Core\GameOption\Values\GameOptionValueForfeitAfterGeneric;
use MyDramGames\Core\GamePlay\GamePlay;
use MyDramGames\Core\GamePlay\GamePlayRepository;
use MyDramGames\Utils\Exceptions\CollectionException;
use MyDramGames\Utils\Player\Player;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GamePlayDisconnectionController extends Controller
{
    use DispatchGamePlayMovedEventTrait;

    public const string MESSAGE_INCORRECT_INPUTS = 'Incorrect inputs';
    public const string MESSAGE_FINISHED = 'Gameplay already finished';
    public const string MESSAGE_FORFEIT_AFTER_DISABLED = 'Option disabled';
    public const string MESSAGE_FORFEIT_AFTER_EARLY = 'Not yet expired';

    public function __construct(
        readonly private GamePlayRepository $gamePlayRepository,
        readonly private GamePlayDisconnectionRepository $gamePlayDisconnectionRepository,
        readonly private GamePlayDisconnectionFactory $gamePlayDisconnectionFactory,
    )
    {

    }

    public function disconnect(Player $player, Request $request, int|string $gamePlayId): Response
    {
        try {
            DB::beginTransaction();

            $gamePlay = $this->gamePlayRepository->getOne($gamePlayId);

            if (!$gamePlay->getPlayers()->exist($player->getId())) {
                DB::rollBack();
                return new Response(static::MESSAGE_FORBIDDEN, SymfonyResponse::HTTP_FORBIDDEN);
            }

            if ($gamePlay->isFinished()) {
                DB::rollBack();
                return new Response(static::MESSAGE_FINISHED, SymfonyResponse::HTTP_BAD_REQUEST);
            }

            $disconnectedPlayer = $this->getValidatedDisconnectedPlayer($request, $gamePlay);
            $disconnection = $this->gamePlayDisconnectionRepository->getOneByGamePlayAndPlayer($gamePlay, $disconnectedPlayer);

            if ($disconnection === null) {
                $this->gamePlayDisconnectionFactory->create($gamePlay, $disconnectedPlayer);
            } else {
                $disconnection->setDisconnectedAt();
                $disconnection->save();
            }

            GamePlayDisconnectedEvent::dispatch($gamePlay, $disconnectedPlayer);

            DB::commit();

            return new Response([], 200);

        } catch (GamePlayStorageException|GamePlayDisconnectException) {
            DB::rollBack();
            return new Response(static::MESSAGE_NOT_FOUND, SymfonyResponse::HTTP_NOT_FOUND);

        } catch (ControllerException $e) {
            DB::rollBack();
            return new Response(['message' => $e->getMessage()], SymfonyResponse::HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpException(SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, static::MESSAGE_INTERNAL_ERROR);
        }
    }

    public function connect(Player $player, int|string $gamePlayId): Response
    {
        try {

            DB::beginTransaction();

            $gamePlay = $this->gamePlayRepository->getOne($gamePlayId);

            if (!$gamePlay->getPlayers()->exist($player->getId())) {
                DB::rollBack();
                return new Response(static::MESSAGE_FORBIDDEN, SymfonyResponse::HTTP_FORBIDDEN);
            }

            if ($gamePlay->isFinished()) {
                DB::rollBack();
                return new Response(static::MESSAGE_FINISHED, SymfonyResponse::HTTP_BAD_REQUEST);
            }

            $this
                ->gamePlayDisconnectionRepository
                ->getOneByGamePlayAndPlayer($gamePlay, $player)
                ?->remove();

            DB::commit();

            return new Response([], 200);

        } catch (GamePlayStorageException) {
            return new Response(static::MESSAGE_NOT_FOUND, SymfonyResponse::HTTP_NOT_FOUND);

        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpException(SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, static::MESSAGE_INTERNAL_ERROR);
        }
    }

    public function forfeitAfterDisconnection(Player $player, Request $request, int|string $gamePlayId): Response
    {
        try {

            DB::beginTransaction();

            $gamePlay = $this->gamePlayRepository->getOne($gamePlayId);

            if (!$gamePlay->getPlayers()->exist($player->getId())) {
                DB::rollBack();
                return new Response(static::MESSAGE_FORBIDDEN, SymfonyResponse::HTTP_FORBIDDEN);
            }

            if ($gamePlay->isFinished()) {
                DB::rollBack();
                return new Response(static::MESSAGE_FINISHED, SymfonyResponse::HTTP_BAD_REQUEST);
            }

            $forfeitAfterOptionValue = $gamePlay
                ->getGameInvite()
                ->getGameSetup()
                ->getOption('forfeitAfter')
                ->getConfiguredValue();

            if ($forfeitAfterOptionValue === GameOptionValueForfeitAfterGeneric::Disabled) {
                DB::rollBack();
                return new Response(static::MESSAGE_FORFEIT_AFTER_DISABLED, SymfonyResponse::HTTP_BAD_REQUEST);
            }

            $disconnectedPlayer = $this->getValidatedDisconnectedPlayer($request, $gamePlay);
            $disconnection = $this->gamePlayDisconnectionRepository->getOneByGamePlayAndPlayer($gamePlay, $disconnectedPlayer);

            if ($disconnection === null) {
                DB::rollBack();
                return new Response(static::MESSAGE_FORFEIT_AFTER_EARLY, SymfonyResponse::HTTP_BAD_REQUEST);
            }

            if (!$disconnection->hasExpired($forfeitAfterOptionValue->getValue())) {
                DB::rollBack();
                return new Response(static::MESSAGE_FORFEIT_AFTER_EARLY, SymfonyResponse::HTTP_BAD_REQUEST);
            }

            $gamePlay->handleForfeit($disconnectedPlayer);

            $this->dispatchGamePlayMovedEvent($gamePlay);

            DB::commit();

            return new Response([], 200);

        } catch (GamePlayStorageException) {
            DB::rollBack();
            return new Response(static::MESSAGE_NOT_FOUND, SymfonyResponse::HTTP_NOT_FOUND);

        } catch (ControllerException|GameSetupException $e) {
            DB::rollBack();
            return new Response(['message' => $e->getMessage()], SymfonyResponse::HTTP_BAD_REQUEST);

        } catch (Exception) {
            DB::rollBack();
            throw new HttpException(SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, static::MESSAGE_INTERNAL_ERROR);
        }

    }

    /**
     * @throws ControllerException|CollectionException
     */
    private function getValidatedDisconnectedPlayer(Request $request, GamePlay $gamePlay): Player
    {
        $singlePlayerCollection = $gamePlay
            ->getPlayers()
            ->filter(fn($item) => $item->getName() === $request->get('disconnected'));

        if ($singlePlayerCollection->count() === 0) {
            throw new ControllerException(static::MESSAGE_INCORRECT_INPUTS);
        }

        return $singlePlayerCollection->pullFirst();
    }
}
