<?php

namespace App\GameCore\GameSetup;

use App\GameCore\Services\Collection\Collection;

abstract class GameSetupAbsFactoryBase implements GameSetupAbsFactory
{
    public function __construct(protected Collection $collectionHandler)
    {

    }
    abstract public function create(): GameSetup;
}
