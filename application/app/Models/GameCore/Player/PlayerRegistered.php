<?php

namespace App\Models\GameCore\Player;

interface PlayerRegistered extends Player
{
    /**
     * @return true
     */
    public function isRegistered(): bool;
}
