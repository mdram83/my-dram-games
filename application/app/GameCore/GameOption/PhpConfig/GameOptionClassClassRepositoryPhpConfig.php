<?php

namespace App\GameCore\GameOption\PhpConfig;

use App\GameCore\GameOption\GameOptionClassRepository;
use Illuminate\Support\Facades\Config;
use MyDramGames\Core\Exceptions\GameOptionException;

class GameOptionClassClassRepositoryPhpConfig implements GameOptionClassRepository
{
    /**
     * @throws GameOptionException
     */
    public function getOne(string $key): string
    {
        if (!$option = Config::get('game-options.' . $key)) {
            throw new GameOptionException(GameOptionException::MESSAGE_INCOMPATIBLE_VALUE);
        }
        return $option;
    }
}
