<?php

namespace App\Models\GameCore\Player;

interface PlayerRepository
{
    public function getOneCurrent(): Player;
}
