<?php

namespace App\GameCore\GamePlay\PhpConfig;

use App\GameCore\GameBox\GameBoxException;
use App\GameCore\GamePlay\GamePlay;
use App\GameCore\GamePlay\GamePlayAbsRepositoryRepository;
use App\GameCore\GamePlay\GamePlayException;
use Illuminate\Support\Facades\Config;

class GamePlayAbsRepositoryRepositoryPhpConfig implements GamePlayAbsRepositoryRepository
{
    public const GAME_PLAY_ABS_CLASS_KEY = 'GamePlayAbs';

    /**
     * @throws GameBoxException
     * @throws GamePlayException
     */
    public function getOne(string $slug): string
    {
        if (!Config::get('games.box.' . $slug)) {
            throw new GameBoxException(GameBoxException::MESSAGE_GAME_BOX_MISSING);
        }

        $className = Config::get("games.box.$slug." . self::GAME_PLAY_ABS_CLASS_KEY);

        if ($this->isEntryMissing($className) || $this->isClassMissing($className) || !$this->isClassCorrectType($className)) {
            throw new GamePlayException(GamePlayException::MESSAGE_NO_ABS_CLASS);
        }

        return $className;
    }

    private function isEntryMissing(?string $className): bool
    {
        return $className === null;
    }

    private function isClassMissing(string $className): bool
    {
        return !class_exists($className);
    }

    private function isClassCorrectType(string $className): bool
    {
        return in_array(GamePlay::class, class_implements($className));
    }
}
