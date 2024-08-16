<?php

namespace App\GameCore\Player;

interface PlayerAnonymous extends Player
{
    /**
     * @return false
     */
    public function isRegistered(): bool;

    /**
     * @return false
     */
    public function isPremium(): bool;
}
