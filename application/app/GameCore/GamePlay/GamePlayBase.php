<?php

namespace App\GameCore\GamePlay;

use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GamePlayStorage\GamePlayStorage;
use App\GameCore\GamePlayStorage\GamePlayStorageException;
use App\GameCore\GameRecord\GameRecordFactory;
use App\GameCore\Services\Collection\Collection;
use MyDramGames\Utils\Player\Player;
use MyDramGames\Utils\Player\PlayerCollection;
use MyDramGames\Utils\Player\PlayerCollectionGeneric;

abstract class GamePlayBase implements GamePlay
{
    protected PlayerCollection $players;
    protected Collection $collectionHandler;
    protected GameRecordFactory $gameRecordFactory;

    protected const GAME_MOVE_CLASS = null;

    protected ?Player $activePlayer;

    /**
     * @throws GamePlayException
     */
    public function __construct(
        protected GamePlayStorage $storage,
        GamePlayServicesProvider $gamePlayServicesProvider,
    )
    {
        $this->configureGamePlayServices($gamePlayServicesProvider);
        $this->validateStorage();
        $this->setPlayers();
        $this->configureServicesAfterhooks();

        if (!$this->storage->getSetup()) {
            $this->initialize();
            $this->storage->setSetup();
        } else {
            $this->loadData();
        }
    }

    /**
     * @throws GamePlayException
     */
    protected function validateMove(GameMove $move): void
    {
        if (!$this->isMoveValidClass($move)) {
            throw new GamePlayException(GamePlayException::MESSAGE_INCOMPATIBLE_MOVE);
        }

        if (!$this->isMoveForActivePlayer($move)) {
            throw new GamePlayException(GamePlayException::MESSAGE_NOT_CURRENT_PLAYER);
        }
    }

    abstract public function handleMove(GameMove $move): void;
    abstract public function handleForfeit(Player $player): void;
    abstract public function getSituation(Player $player): array;

    abstract protected function initialize(): void;
    abstract protected function saveData(): void;
    abstract protected function loadData(): void;

    final protected function configureGamePlayServices(GamePlayServicesProvider $provider): void
    {
        $this->configureMandatoryGamePlayServices($provider);
        $this->configureOptionalGamePlayServices($provider);
    }

    final protected function configureMandatoryGamePlayServices(GamePlayServicesProvider $provider): void
    {
        $this->collectionHandler = $provider->getCollectionHandler();
        $this->gameRecordFactory = $provider->getGameRecordFactory();
    }

    abstract protected function configureOptionalGamePlayServices(GamePlayServicesProvider $provider): void;

    abstract protected function configureServicesAfterHooks(): void;

    final public function getId(): int|string
    {
        return $this->storage->getId();
    }

    final protected function isMoveValidClass(GameMove $move): bool
    {
        return is_a($move, $this::GAME_MOVE_CLASS);
    }

    final protected function isMoveForActivePlayer(GameMove $move): bool
    {
        return $move->getPlayer()->getId() === $this->getActivePlayer()?->getId();
    }

    final public function getPlayers(): PlayerCollection
    {
        return $this->players;
    }

    public function getActivePlayer(): ?Player
    {
        return $this->activePlayer;
    }

    final public function getGameInvite(): GameInvite
    {
        return $this->storage->getGameInvite();
    }

    final public function isFinished(): bool
    {
        return $this->storage->getFinished();
    }

    final protected function getPlayerByName(?string $playerName): ?Player
    {
        if ($playerName === null) {
            return null;
        }

        $playerId = array_keys(array_filter(
            $this->players->toArray(),
            fn($item) => $item->getName() === $playerName
        ))[0];

        return $this->players->getOne($playerId);
    }

    final protected function setPlayers(): void
    {
        if (!isset($this->players)) {
            $this->players = new PlayerCollectionGeneric($this->storage->getGameInvite()->getPlayers());

            // TODO in below I had collection handler set in ServiceProvider
            // TODO now I need to register new ServiceProvider for Utilities and register each collection separately
            // TODO then I need to pass required collections in constructor here
            // TODO and then I need to populate them with data in this setter - instead of hardcoding above class...
//            $this->players = new CollectionGamePlayPlayers(
//                clone $this->collectionHandler,
//                $this->storage->getGameInvite()->getPlayers()
//            );
        }
    }

    /**
     * @throws GamePlayException
     */
    final protected function validateStorage(): void
    {
        try {
            $gameInvite = $this->storage->getGameInvite();
        } catch (GamePlayStorageException) {
            throw new GamePlayException(GamePlayException::MESSAGE_STORAGE_INCORRECT);
        }

        if (
            $gameInvite->getGameSetup()->getNumberOfPlayers()->getConfiguredValue()->getValue()
            !== count($gameInvite->getPlayers())
        ) {
            throw new GamePlayException(GamePlayException::MESSAGE_MISSING_PLAYERS);
        }
    }

    /**
     * @throws GamePlayException
     */
    final protected function validateActionOnFinishedGame(): void
    {
        if ($this->isFinished()) {
            throw new GamePlayException(GamePlayException::MESSAGE_MOVE_ON_FINISHED_GAME);
        }
    }

    /**
     * @throws GamePlayException
     */
    final protected function validateGamePlayer(Player $player): void
    {
        if (!$this->getPlayers()->exist($player->getId())) {
            throw new GamePlayException(GamePlayException::MESSAGE_NOT_PLAYER);
        }
    }
}
