<?php

namespace App\Models\GameCore\Player;

interface PlayerAnonymous extends Player
{
    /**
     * @return false
     */
    public function isRegistered(): bool;
}
