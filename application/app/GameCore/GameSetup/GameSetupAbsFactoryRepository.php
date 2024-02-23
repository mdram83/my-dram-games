<?php

namespace App\GameCore\GameSetup;

interface GameSetupAbsFactoryRepository
{
    public function getOne(string $slug): GameSetupAbsFactory;
}
