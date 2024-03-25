<?php

namespace App\GameCore\GameElements\GameMove\PhpConfig;

use App\GameCore\GameBox\GameBoxException;
use App\GameCore\GameElements\GameMove\GameMoveAbsFactory;
use App\GameCore\GameElements\GameMove\GameMoveAbsFactoryRepository;
use App\GameCore\GameElements\GameMove\GameMoveException;
use Illuminate\Support\Facades\Config;

class GameMoveAbsFactoryRepositoryPhpConfig implements GameMoveAbsFactoryRepository
{
    public const GAME_MOVE_ABS_FACTORY_KEY = 'GameMoveAbsFactory';

    /**
     * @throws GameBoxException
     * @throws GameMoveException
     */
    public function getOne(string $slug): GameMoveAbsFactory
    {
        if (!Config::get('games.box.' . $slug)) {
            throw new GameBoxException(GameBoxException::MESSAGE_GAME_BOX_MISSING);
        }

        $className = Config::get("games.box.$slug." . self::GAME_MOVE_ABS_FACTORY_KEY);

        if ($this->isEntryMissing($className) || $this->isClassMissing($className) || !$this->isClassCorrectType($className)) {
            throw new GameMoveException(GameMoveException::MESSAGE_NO_ABS_FACTORY);
        }

        return new $className();
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
        return in_array(GameMoveAbsFactory::class, class_implements($className));
    }
}
