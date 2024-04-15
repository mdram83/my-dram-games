<?php

namespace App\GameCore\GamePlay;

use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameElements\GamePlayPlayers\CollectionGamePlayPlayers;
use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GamePlayStorage\GamePlayStorage;
use App\GameCore\GamePlayStorage\GamePlayStorageException;
use App\GameCore\GameRecord\GameRecordFactory;
use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\Collection;

abstract class GamePlayBase implements GamePlay
{
    protected CollectionGamePlayPlayers $players;

    /**
     * @throws GamePlayException
     */
    public function __construct(
        protected GamePlayStorage $storage,
        protected Collection $collectionHandler,
        protected GameRecordFactory $gameRecordFactory,
    )
    {
        $this->validateStorage();
        $this->setPlayers();

        if (!$this->storage->getSetup()) {
            $this->initialize();
            $this->storage->setSetup();
        } else {
            $this->loadData();
        }
    }

    abstract public function handleMove(GameMove $move): void;
    abstract public function handleForfeit(Player $player): void;
    abstract public function getSituation(Player $player): array;

    abstract protected function initialize(): void;
    abstract protected function saveData(): void;
    abstract protected function loadData(): void;

    final public function getId(): int|string
    {
        return $this->storage->getId();
    }

    final public function getPlayers(): CollectionGamePlayPlayers
    {
        return $this->players;
    }

    final public function getGameInvite(): GameInvite
    {
        return $this->storage->getGameInvite();
    }

    final public function isFinished(): bool
    {
        return $this->storage->getFinished();
    }

    final protected function setPlayers(): void
    {
        if (!isset($this->players)) {
            $this->players = new CollectionGamePlayPlayers(
                clone $this->collectionHandler,
                $this->storage->getGameInvite()->getPlayers()
            );
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
}
