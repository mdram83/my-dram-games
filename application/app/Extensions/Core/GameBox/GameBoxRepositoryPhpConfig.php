<?php

namespace App\Extensions\Core\GameBox;

use Illuminate\Support\Facades\Config;
use MyDramGames\Core\Exceptions\GameBoxException;
use MyDramGames\Core\GameBox\GameBox;
use MyDramGames\Core\GameBox\GameBoxCollection;
use MyDramGames\Core\GameBox\GameBoxRepository;
use MyDramGames\Core\GameSetup\GameSetupRepository;
use MyDramGames\Utils\Exceptions\CollectionException;

class GameBoxRepositoryPhpConfig implements GameBoxRepository
{
    public function __construct(
        private readonly GameSetupRepository $gameSetupRepository,
        private readonly GameBoxCollection $gameBoxCollection,
    )
    {

    }

    /**
     * @param string $slug
     * @return GameBox
     * @throws GameBoxException
     */
    public function getOne(string $slug): GameBox
    {
        return new GameBoxPhpConfig($slug, $this->gameSetupRepository);
    }

    /**
     * @return GameBoxCollection
     * @throws GameBoxException
     * @throws CollectionException
     */
    public function getAll(): GameBoxCollection
    {
        return $this->gameBoxCollection->clone()->reset(array_map(
            fn($slug) => $this->getOne($slug),
            array_keys(Config::get('games.box'))
        ));

//        return array_map(
//            fn($slug) => new GameBoxPhpConfig($slug, $this->gameSetupRepository->getOne($slug)->create()),
//            array_keys(Config::get('games.box'))
//        );
    }
}
