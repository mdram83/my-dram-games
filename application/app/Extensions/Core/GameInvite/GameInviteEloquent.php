<?php

namespace App\Extensions\Core\GameInvite;

use App\Models\GameInviteEloquentModel;
use Illuminate\Database\Eloquent\Model;
use MyDramGames\Core\Exceptions\GameBoxException;
use MyDramGames\Core\Exceptions\GameInviteException;
use MyDramGames\Core\Exceptions\GameOptionException;
use MyDramGames\Core\Exceptions\GameSetupException;
use MyDramGames\Core\GameBox\GameBox;
use MyDramGames\Core\GameBox\GameBoxRepository;
use MyDramGames\Core\GameInvite\GameInvite;
use MyDramGames\Core\GameOption\GameOptionConfigurationCollection;
use MyDramGames\Core\GameSetup\GameSetup;
use MyDramGames\Utils\Exceptions\CollectionException;
use MyDramGames\Utils\Player\Player;
use MyDramGames\Utils\Player\PlayerCollection;

class GameInviteEloquent implements GameInvite
{
    protected GameInviteEloquentModel $model;
    protected GameBox $gameBox;
    protected GameSetup $gameSetup;

    /**
     * @throws GameInviteException
     */
    public function __construct(
        protected GameBoxRepository $gameBoxRepository,
        protected PlayerCollection $playersHandler,
        protected GameOptionConfigurationCollection $optionsHandler,
        string $id = null
    )
    {
        if ($id === null) {
            $this->registerNewModel();
        } else {
            $this->loadExisingModel($id);
        }
    }

    public function getId(): string|int
    {
        return $this->model->id;
    }

    /**
     * @param Player $player
     * @param bool $host
     * @throws CollectionException
     * @throws GameBoxException
     * @throws GameInviteException
     * @throws GameOptionException
     * @throws GameSetupException
     */
    public function addPlayer(Player $player, bool $host = false): void
    {
        if (!is_a($player, Model::class)) {
            throw new GameInviteException(GameInviteException::MESSAGE_PLAYER_TYPE_UNSET);
        }

        if (!$this->hasGameSetup()) {
            throw new GameInviteException(GameInviteException::MESSAGE_GAME_SETUP_NOT_SET);
        }

        if ($this->isPlayer($player)) {
            throw new GameInviteException(GameInviteException::MESSAGE_PLAYER_ALREADY_ADDED);
        }

        if (!$this->canAddMorePlayers()) {
            throw new GameInviteException(GameInviteException::MESSAGE_TOO_MANY_PLAYERS);
        }

        if ($host === true && $this->hasHost()) {
            throw new GameInviteException(GameInviteException::MESSAGE_HOST_ALREADY_ADDED);
        }

        if ($host === false && !$this->hasPlayers()) {
            throw new GameInviteException(GameInviteException::MESSAGE_HOST_NOT_SET);
        }

        if ($host === true) {
            $this->model->hostable()->associate($player);
            $this->saveModel();
        }

        if ($player->isRegistered() === true) {
            $this->model->playersRegistered()->attach($player->getId());

        } elseif ($player->isRegistered() === false) {
            $this->model->playersAnonymous()->attach($player->getId());

        } else {
            throw new GameInviteException(GameInviteException::MESSAGE_PLAYER_TYPE_UNSET);
        }

        $this->model->refresh();
    }

    public function getPlayers(): PlayerCollection
    {
        return $this->playersHandler->clone()->reset(array_merge(
            $this->model->playersRegistered->all(),
            $this->model->playersAnonymous->all()
        ));
    }

    /**
     * @throws GameInviteException
     */
    public function getHost(): Player
    {
        if (!$this->hasHost()) {
            throw new GameInviteException(GameInviteException::MESSAGE_HOST_NOT_SET);
        }

        return $this->model->hostable;
    }

    /**
     * @throws GameInviteException
     */
    public function isHost(Player $player): bool
    {
        return $this->getHost()->getId() === $player->getId();
    }

    /**
     * @throws GameInviteException|GameBoxException
     */
    public function setGameBox(GameBox $gameBox): void
    {
        if ($this->hasGameBox()) {
            throw new GameInviteException(GameInviteException::MESSAGE_GAME_BOX_ALREADY_SET);
        }

        $this->gameBox = $gameBox;
        $this->model->gameBox = $gameBox->getSlug();
        $this->saveModel();
    }

    /**
     * @throws GameInviteException
     */
    public function getGameBox(): GameBox
    {
        if (!$this->hasGameBox()) {
            throw new GameInviteException(GameInviteException::MESSAGE_GAME_BOX_NOT_SET);
        }

        if (!isset($this->gameBox)) {
            $slug = $this->model->gameBox;
            $this->gameBox = $this->gameBoxRepository->getOne($slug);
        }

        return $this->gameBox;
    }

    /**
     * @param GameOptionConfigurationCollection $options
     * @throws GameBoxException|GameInviteException|GameSetupException
     */
    public function setOptions(GameOptionConfigurationCollection $options): void
    {
        if (!$this->hasGameBox()) {
            throw new GameInviteException(GameInviteException::MESSAGE_GAME_SETUP_NOT_SET);
        }

        if ($this->hasGameSetup()) {
            throw new GameInviteException(GameInviteException::MESSAGE_GAME_SETUP_ALREADY_SET);
        }

        $this->setAndConfigureGameSetup($options);
        $this->model->options = $this->encodeOptions($options->toArray());
        $this->saveModel();
    }

    /**
     * @return GameSetup
     * @throws CollectionException|GameBoxException|GameInviteException|GameSetupException
     */
    public function getGameSetup(): GameSetup
    {
        if (!$this->hasGameSetup()) {
            throw new GameInviteException(GameInviteException::MESSAGE_GAME_SETUP_NOT_SET);
        }

        if (!isset($this->gameSetup)) {

            $options = $this->optionsHandler->clone()->reset($this->decodeOptions($this->model->options));
            $this->setAndConfigureGameSetup($options);
        }

        return $this->gameSetup;
    }

    /**
     * @return array
     * @throws CollectionException|GameBoxException|GameInviteException|GameSetupException
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'host' => ['name' => $this->getHost()->getName()],
            'options' => array_map(fn($option) => $option->getConfiguredValue(), $this->getGameSetup()->getAllOptions()->toArray()),
            'players' => array_values(array_map(fn($player) => ['name' => $player->getName()], $this->getPlayers()->toArray())),
        ];
    }

    /**
     * @param GameOptionConfigurationCollection $options
     * @throws GameBoxException|GameInviteException|GameSetupException
     */
    protected function setAndConfigureGameSetup(GameOptionConfigurationCollection $options): void
    {
        $this->gameSetup = clone $this->getGameBox()->getGameSetup();
        $this->gameSetup->configureOptions($options);
    }

    protected function encodeOptions(array $options): string
    {
        $serialized = array_map(fn($option) => serialize($option), $options);
        return json_encode($serialized);
    }

    protected function decodeOptions(string $options): array
    {
        $decoded = json_decode($options, true);
        return array_map(fn($option) => unserialize($option), $decoded);
    }

    /**
     * @return bool
     * @throws CollectionException
     * @throws GameBoxException|GameInviteException|GameOptionException|GameSetupException
     */
    protected function canAddMorePlayers(): bool
    {
        return $this->getPlayers()->count() < $this->getGameSetup()->getNumberOfPlayers()->getConfiguredValue()->getValue();
    }

    public function isPlayer(Player $player): bool
    {
        return $this->getPlayers()->exist($player->getId());
    }

    protected function hasHost(): bool
    {
        return isset($this->model->hostable);
    }

    protected function hasGameBox(): bool
    {
        return isset($this->model->gameBox);
    }

    protected function hasGameSetup(): bool
    {
        return isset($this->model->options);
    }

    protected function saveModel(): void
    {
        $this->model->save();
    }

    protected function hasPlayers(): bool
    {
        return !$this->getPlayers()->isEmpty();
    }

    protected function registerNewModel(): void
    {
        $this->model = new GameInviteEloquentModel();
        $this->saveModel();
    }

    /**
     * @throws GameInviteException
     */
    protected function loadExisingModel(string $id): void
    {
        if (!($model = GameInviteEloquentModel::where('id', $id)->first())) {
            throw new GameInviteException(GameInviteException::MESSAGE_GAME_NOT_FOUND);
        }
        $this->model = $model;
    }
}
