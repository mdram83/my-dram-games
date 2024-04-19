<?php

namespace App\Games\Thousand\Elements;

use App\GameCore\GameElements\GamePhase\GamePhaseException;

class GamePhaseThousandRepository
{
    private array $repository;

    public function __construct()
    {
        $this->repository = [
            GamePhaseThousandBidding::PHASE_KEY => GamePhaseThousandBidding::class,
            GamePhaseThousandStockDistribution::PHASE_KEY => GamePhaseThousandStockDistribution::class,
            GamePhaseThousandDeclaration::PHASE_KEY => GamePhaseThousandDeclaration::class,
            GamePhaseThousandPlayFirstCard::PHASE_KEY  => GamePhaseThousandPlayFirstCard::class,
            GamePhaseThousandPlaySecondCard::PHASE_KEY  => GamePhaseThousandPlaySecondCard::class,
            GamePhaseThousandPlayThirdCard::PHASE_KEY  => GamePhaseThousandPlayThirdCard::class,
            GamePhaseThousandCountPoints::PHASE_KEY  => GamePhaseThousandCountPoints::class,
        ];
    }

    /**
     * @throws GamePhaseException
     */
    public function getOne(string $key): GamePhaseThousand
    {
        if (!isset($this->repository[$key])) {
            throw new GamePhaseException(GamePhaseException::MESSAGE_INCORRECT_KEY);
        }

        $className = $this->repository[$key];
        return new $className();
    }
}
