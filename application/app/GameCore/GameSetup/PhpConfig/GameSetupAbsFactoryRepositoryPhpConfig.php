<?php

namespace App\GameCore\GameSetup\PhpConfig;

use App\GameCore\GameSetup\GameSetupAbsFactory;
use App\GameCore\GameSetup\GameSetupAbsFactoryRepository;
use App\GameCore\GameSetup\GameSetupException;
use Illuminate\Support\Facades\Config;

class GameSetupAbsFactoryRepositoryPhpConfig implements GameSetupAbsFactoryRepository
{
    public const GAME_SETUP_ABS_FACTORY_KEY = 'GameSetupAbsFactory';

    /**
     * @throws GameSetupException
     */
    public function getOne(string $slug): GameSetupAbsFactory
    {
        $className = Config::get("games.box.$slug." . self::GAME_SETUP_ABS_FACTORY_KEY);

        if ($this->isEntryMissing($className) || $this->isClassMissing($className) || !$this->isClassCorrectType($className)) {
            throw new GameSetupException(GameSetupException::MESSAGE_NO_ABS_FACTORY);
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
        return in_array(GameSetupAbsFactory::class, class_implements($className));
    }
}
