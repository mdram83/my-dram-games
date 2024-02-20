<?php

namespace App\GameCore\Player;

interface PlayerRegistered extends Player
{
    /**
     * @return true
     */
    public function isRegistered(): bool;
}
