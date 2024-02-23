<?php

namespace App\GameCore\GameSetup;

interface GameSetupAbsFactory
{
    public function create(array $options = []): GameSetup;
}
