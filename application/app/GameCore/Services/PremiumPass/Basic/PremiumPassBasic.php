<?php

namespace App\GameCore\Services\PremiumPass\Basic;

use App\GameCore\GameBox\GameBoxRepository;
use App\GameCore\Player\Player;
use App\GameCore\Services\PremiumPass\PremiumPass;
use App\GameCore\Services\PremiumPass\PremiumPassException;

class PremiumPassBasic implements PremiumPass
{
    public function __construct(private readonly GameBoxRepository $gameBoxRepository)
    {

    }

    /**
     * @throws PremiumPassException
     */
    public function validate(string $slug, Player $player): void
    {
        $gameBox = $this->gameBoxRepository->getOne($slug);

        if (!$gameBox->isPremium()) {
            return;
        }

        if (!$player->isPremium()) {
            throw new PremiumPassException(PremiumPassException::MESSAGE_MISSING_PREMIUM_PASS);
        }
    }
}
