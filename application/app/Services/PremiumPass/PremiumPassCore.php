<?php

namespace App\Services\PremiumPass;

use MyDramGames\Core\GameBox\GameBoxRepository;
use MyDramGames\Utils\Player\Player;

readonly class PremiumPassCore implements PremiumPass
{
    public function __construct(private GameBoxRepository $gameBoxRepository)
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
