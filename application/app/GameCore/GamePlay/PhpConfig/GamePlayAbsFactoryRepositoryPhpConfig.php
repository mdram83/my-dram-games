<?php

namespace App\GameCore\GamePlay\PhpConfig;

use App\GameCore\GameBox\GameBoxException;
use App\GameCore\GamePlay\GamePlayAbsFactory;
use App\GameCore\GamePlay\GamePlayAbsFactoryRepository;
use App\GameCore\GamePlay\GamePlayException;
use App\GameCore\GamePlayStorage\GamePlayStorageFactory;
use App\GameCore\Services\Collection\Collection;
use Illuminate\Support\Facades\Config;

class GamePlayAbsFactoryRepositoryPhpConfig implements GamePlayAbsFactoryRepository
{
    public const GAME_PLAY_ABS_FACTORY_KEY = 'GamePlayAbsFactory';

    public function __construct(
        readonly private GamePlayStorageFactory $storageFactory,
        readonly private Collection $collectionHandler
    )
    {

    }

    /**
     * @throws GameBoxException
     * @throws GamePlayException
     */
    public function getOne(string $slug): GamePlayAbsFactory
    {
        if (!Config::get('games.box.' . $slug)) {
            throw new GameBoxException(GameBoxException::MESSAGE_GAME_BOX_MISSING);
        }

        $className = Config::get("games.box.$slug." . self::GAME_PLAY_ABS_FACTORY_KEY);

        if ($this->isEntryMissing($className) || $this->isClassMissing($className) || !$this->isClassCorrectType($className)) {
            throw new GamePlayException(GamePlayException::MESSAGE_NO_ABS_FACTORY);
        }

        return new $className($this->storageFactory, clone $this->collectionHandler);
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
        return in_array(GamePlayAbsFactory::class, class_implements($className));
    }
}
