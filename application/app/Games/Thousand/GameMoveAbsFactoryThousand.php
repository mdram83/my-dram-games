<?php

namespace App\Games\Thousand;

use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameElements\GameMove\GameMoveAbsFactory;
use App\GameCore\GameElements\GameMove\GameMoveException;
use App\GameCore\GameElements\GamePhase\GamePhaseException;
use App\GameCore\Player\Player;
use App\Games\Thousand\Elements\GameMoveThousand;
use App\Games\Thousand\Elements\GameMoveThousandBidding;
use App\Games\Thousand\Elements\GameMoveThousandCollectTricks;
use App\Games\Thousand\Elements\GameMoveThousandCountPoints;
use App\Games\Thousand\Elements\GameMoveThousandDeclaration;
use App\Games\Thousand\Elements\GameMoveThousandPlayCard;
use App\Games\Thousand\Elements\GameMoveThousandSorting;
use App\Games\Thousand\Elements\GameMoveThousandStockDistribution;
use App\Games\Thousand\Elements\GamePhaseThousand;
use App\Games\Thousand\Elements\GamePhaseThousandBidding;
use App\Games\Thousand\Elements\GamePhaseThousandCollectTricks;
use App\Games\Thousand\Elements\GamePhaseThousandCountPoints;
use App\Games\Thousand\Elements\GamePhaseThousandDeclaration;
use App\Games\Thousand\Elements\GamePhaseThousandPlayFirstCard;
use App\Games\Thousand\Elements\GamePhaseThousandPlaySecondCard;
use App\Games\Thousand\Elements\GamePhaseThousandPlayThirdCard;
use App\Games\Thousand\Elements\GamePhaseThousandRepository;
use App\Games\Thousand\Elements\GamePhaseThousandStockDistribution;
use App\Games\TicTacToe\GameMoveTicTacToe;

class GameMoveAbsFactoryThousand implements GameMoveAbsFactory
{
    private GamePhaseThousandRepository $phaseRepository;

    public function __construct()
    {
        $this->phaseRepository = new GamePhaseThousandRepository();
    }

    /**
     * @throws GameMoveException
     */
    public function create(Player $player, array $inputs): GameMove
    {
        [$phaseKey, $data, $phase] = $this->getValidatedInputs($inputs);

        if ($phaseKey === 'sorting') {
            return new GameMoveThousandSorting($player, $data, $phase);
        }

        $className = $this->getPhaseRelatedMoveClass($phase);
        return new $className($player, $data, $phase);
    }

    /**
     * @throws GameMoveException
     */
    private function getValidatedInputs(array $inputs): array
    {
        $phaseKey = $inputs['phase'] ?? null;
        $data = $inputs['data'] ?? null;

        if (!isset($phaseKey) || $phaseKey === '' || !isset($data) || !is_array($data)) {
            throw new GameMoveException(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);
        }

        try {
            $phase = $this->phaseRepository->getOne($phaseKey);
        } catch (GamePhaseException) {

        }

        if (!isset($phase) && $phaseKey !== 'sorting') {
            throw new GameMoveException(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);
        }

        return [$phaseKey, $data, $phase ?? null];
    }

    private function getPhaseRelatedMoveClass(GamePhaseThousand $phase): string
    {
        $moves = [
            GamePhaseThousandBidding::PHASE_KEY => GameMoveThousandBidding::class,
            GamePhaseThousandStockDistribution::PHASE_KEY => GameMoveThousandStockDistribution::class,
            GamePhaseThousandDeclaration::PHASE_KEY => GameMoveThousandDeclaration::class,
            GamePhaseThousandPlayFirstCard::PHASE_KEY  => GameMoveThousandPlayCard::class,
            GamePhaseThousandPlaySecondCard::PHASE_KEY  => GameMoveThousandPlayCard::class,
            GamePhaseThousandPlayThirdCard::PHASE_KEY  => GameMoveThousandPlayCard::class,
            GamePhaseThousandCollectTricks::PHASE_KEY  => GameMoveThousandCollectTricks::class,
            GamePhaseThousandCountPoints::PHASE_KEY  => GameMoveThousandCountPoints::class,
        ];

        return $moves[$phase->getKey()];
    }
}
