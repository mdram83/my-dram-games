<?php

namespace App\GameCore\Services\PremiumPass;

use App\GameCore\Player\Player;

interface PremiumPass
{
    /**
     * @throws PremiumPassException
     */
    public function validate(string $slug, Player $player): void;
}