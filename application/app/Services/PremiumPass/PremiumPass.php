<?php

namespace App\Services\PremiumPass;

use MyDramGames\Utils\Player\Player;

interface PremiumPass
{
    /**
     * @throws PremiumPassException
     */
    public function validate(string $slug, Player $player): void;
}
