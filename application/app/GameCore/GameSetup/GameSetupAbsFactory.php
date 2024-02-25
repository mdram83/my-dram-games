<?php

namespace App\GameCore\GameSetup;

interface GameSetupAbsFactory
{
    public function create(): GameSetup;
}
