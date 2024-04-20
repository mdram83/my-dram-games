<?php

namespace App\Games\Thousand\Elements;

use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameElements\GamePhase\GamePhase;

abstract class GamePhaseThousand implements GamePhase
{
    public const PHASE_KEY = null;
    protected const PHASE_NAME = null;
    protected const PHASE_DESCRIPTION = null;

    final public function getKey(): string
    {
        return $this::PHASE_KEY;
    }

    final public function getName(): string
    {
        return $this::PHASE_NAME;
    }

    final public function getDescription(): string
    {
        return $this::PHASE_DESCRIPTION;
    }

    abstract public function getNextPhase(bool $lastAttempt): GamePhase;
}
