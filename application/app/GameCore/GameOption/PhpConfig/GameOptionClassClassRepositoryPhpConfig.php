<?php

namespace App\GameCore\GameOption\PhpConfig;

use App\GameCore\GameOption\GameOption;
use App\GameCore\GameOption\GameOptionException;
use App\GameCore\GameOption\GameOptionClassRepository;
use Illuminate\Support\Facades\Config;

class GameOptionClassClassRepositoryPhpConfig implements GameOptionClassRepository
{
    /**
     * @throws GameOptionException
     */
    public function getOne(string $key): string
    {
        if (!$option = Config::get('game-options.' . $key)) {
            throw new GameOptionException(GameOptionException::MESSAGE_OPTION_NOT_EXIST);
        }
        return $option;
    }
}
