<?php

namespace App\GameCore\GameBox\PhpConfig;

use App\GameCore\GameBox\GameBox;
use App\GameCore\GameBox\GameBoxException;
use App\GameCore\GameBox\GameBoxRepository;
use Illuminate\Support\Facades\Config;

class GameBoxRepositoryPhpConfig implements GameBoxRepository
{
    /**
     * @param string $slug
     * @return GameBox
     * @throws GameBoxException
     */
    public function getOne(string $slug): GameBox
    {
        return new GameBoxPhpConfig($slug);
    }

    /**
     * @return array<GameBox>
     * @throws GameBoxException
     */
    public function getAll(): array

    {
        return array_map(
            fn($slug) => new GameBoxPhpConfig($slug),
            array_keys(Config::get('games.box'))
        );
    }
}
