<?php

namespace App\Http\Controllers\GameCore;

use App\Events\GamePlay\GamePlayDisconnectedEvent;
use App\Http\Controllers\Traits\ValidateGamePlayNotFinishedTrait;
use App\Http\Controllers\Traits\ValidateGamePlayPlayerTrait;
use App\Services\GamePlayDisconnection\GamePlayDisconnectionFactory;
use App\Services\GamePlayDisconnection\GamePlayDisconnectionRepository;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllerException;
use App\Http\Controllers\Traits\DispatchGamePlayMovedEventTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use MyDramGames\Core\Exceptions\GameOptionException;
use MyDramGames\Core\GameOption\Values\GameOptionValueForfeitAfterGeneric;
use MyDramGames\Core\GamePlay\GamePlay;
use MyDramGames\Core\GamePlay\GamePlayRepository;
use MyDramGames\Utils\Exceptions\CollectionException;
use MyDramGames\Utils\Player\Player;

class GamePlayDisconnectionController extends Controller
{
    use DispatchGamePlayMovedEventTrait;
    use ValidateGamePlayPlayerTrait;
    USE ValidateGamePlayNotFinishedTrait;

    public const string MESSAGE_INCORRECT_INPUTS = 'Incorrect inputs';
    public const string MESSAGE_FORFEIT_AFTER_DISABLED = 'Option disabled';
    public const string MESSAGE_FORFEIT_AFTER_EARLY = 'Not yet expired';

    public function __construct(
        readonly private GamePlayRepository $gamePlayRepository,
        readonly private GamePlayDisconnectionRepository $gamePlayDisconnectionRepository,
        readonly private GamePlayDisconnectionFactory $gamePlayDisconnectionFactory,
    )
    {

    }

    /**
     * @throws ControllerException
     * @throws CollectionException
     */
    public function disconnect(Player $player, Request $request, int|string $gamePlayId): Response
    {
        try {
            DB::beginTransaction();

            $gamePlay = $this->gamePlayRepository->getOne($gamePlayId);

            $this->validateGamePlayPlayer($gamePlay, $player);
            $this->validateGamePlayNotFinished($gamePlay);

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

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function connect(Player $player, int|string $gamePlayId): Response
    {
        try {

            DB::beginTransaction();

            $gamePlay = $this->gamePlayRepository->getOne($gamePlayId);

            $this->validateGamePlayPlayer($gamePlay, $player);
            $this->validateGamePlayNotFinished($gamePlay);

            $this
                ->gamePlayDisconnectionRepository
                ->getOneByGamePlayAndPlayer($gamePlay, $player)
                ?->remove();

            DB::commit();

            return new Response([], 200);

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws GameOptionException
     * @throws ControllerException
     * @throws CollectionException
     */
    public function forfeitAfterDisconnection(Player $player, Request $request, int|string $gamePlayId): Response
    {
        try {

            DB::beginTransaction();

            $gamePlay = $this->gamePlayRepository->getOne($gamePlayId);

            $this->validateGamePlayPlayer($gamePlay, $player);
            $this->validateGamePlayNotFinished($gamePlay);

            $forfeitAfterOptionValue = $gamePlay
                ->getGameInvite()
                ->getGameSetup()
                ->getOption('forfeitAfter')
                ->getConfiguredValue();

            if ($forfeitAfterOptionValue === GameOptionValueForfeitAfterGeneric::Disabled) {
                throw new ControllerException(static::MESSAGE_FORFEIT_AFTER_DISABLED);
            }

            $disconnectedPlayer = $this->getValidatedDisconnectedPlayer($request, $gamePlay);
            $disconnection = $this->gamePlayDisconnectionRepository->getOneByGamePlayAndPlayer($gamePlay, $disconnectedPlayer);

            if ($disconnection === null) {
                throw new ControllerException(static::MESSAGE_FORFEIT_AFTER_EARLY);
            }

            if (!$disconnection->hasExpired($forfeitAfterOptionValue->getValue())) {
                throw new ControllerException(static::MESSAGE_FORFEIT_AFTER_EARLY);
            }

            $gamePlay->handleForfeit($disconnectedPlayer);

            $this->dispatchGamePlayMovedEvent($gamePlay);

            DB::commit();

            return new Response([], 200);

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
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
