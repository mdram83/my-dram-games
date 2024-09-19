<?php

namespace App\Extensions\Core\GameBox;

use Illuminate\Support\Facades\Config;
use MyDramGames\Core\Exceptions\GameBoxException;
use MyDramGames\Core\GameBox\GameBox;
use MyDramGames\Core\GameMove\GameMoveFactory;
use MyDramGames\Core\GamePlay\GamePlay;
use MyDramGames\Core\GameSetup\GameSetup;

class GameBoxPhpConfig implements GameBox
{
    private string $slug;
    private GameSetup $gameSetup;

    private string $name;
    private ?string $description;
    private ?int $durationInMinutes;
    private ?int $minPlayerAge;
    private bool $isActive;
    private bool $isPremium;

    private string $gamePlayClassname;
    private string $gameMoveFactoryClassname;

    /**
     * @throws GameBoxException
     */
    public function __construct(string $slug, GameSetup $gameSetup)
    {
        $this->slug = $slug;
        $this->gameSetup = $gameSetup;

        if (!$box = Config::get('games.box.' . $this->slug)) {
            throw new GameBoxException(GameBoxException::MESSAGE_INCORRECT_CONFIGURATION);
        }

        if (
            !($this->name = $box['name'] ?? '')
            || !($gameSetup->getNumberOfPlayers() ?? [])
            || !isset($box['isActive'])
            || !isset($box['isPremium'])
            || !isset($box['gamePlayClassname'])
            || !isset($box['gameMoveFactoryClassname'])
        ) {
            throw new GameBoxException(GameBoxException::MESSAGE_INCORRECT_CONFIGURATION);
        }

        $this->description = $box['description'] ?? null;
        $this->durationInMinutes = $box['durationInMinutes'] ?? null;
        $this->minPlayerAge = $box['minPlayerAge'] ?? null;

        $this->isActive = $box['isActive'];
        $this->isPremium = $box['isPremium'];

        $this->gamePlayClassname = $box['gamePlayClassname'];
        $this->gameMoveFactoryClassname = $box['gameMoveFactoryClassname'];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getDurationInMinutes(): ?int
    {
        return $this->durationInMinutes;
    }

    public function getMinPlayerAge(): ?int
    {
        return $this->minPlayerAge;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function isPremium(): bool
    {
        return $this->isPremium;
    }

    public function getNumberOfPlayersDescription(): string
    {
        $option = $this->getGameSetup()->getNumberOfPlayers();
        $values = array_map(fn($value) => $value->getValue() , $option->getAvailableValues()->toArray());

        if (!$this->hasConsecutiveNumberOfPlayers($values)) {
            return implode(', ', $values);
        }

        if (count($values) === 1) {
            return $values[0];
        }

        return min($values) . '-' . max($values);
    }

    public function getGameSetup(): GameSetup
    {
        return $this->gameSetup;
    }

    /**
     * @throws GameBoxException
     */
    public function toArray(): array
    {
        return [
            'slug' => $this->getSlug(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'numberOfPlayersDescription' => $this->getNumberOfPlayersDescription(),
            'durationInMinutes' => $this->getDurationInMinutes(),
            'minPlayerAge' => $this->getMinPlayerAge(),
            'isActive' => $this->isActive(),
            'isPremium' => $this->isPremium(),

            'options' => array_map(fn($option) => [
                'availableValues' => array_map(
                    fn($optionValue) => ['label' => $optionValue->getLabel(), 'value' => $optionValue->getValue()],
                    $option->getAvailableValues()->toArray()
                ),
                'defaultValue' => $option->getDefaultValue()->getValue(),
                'type' => $option->getType()->getValue(),
                'name' => $option->getName(),
                'description' => $option->getDescription(),
            ], $this->getGameSetup()->getAllOptions()->toArray()),

        ];
    }

    private function hasConsecutiveNumberOfPlayers(array $numberOfPlayers): bool
    {
        for ($i = 1; $i < count($numberOfPlayers); $i++) {
            if ($numberOfPlayers[$i] - $numberOfPlayers[$i - 1] !== 1) {
                return false;
            }
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getGamePlayClassname(): string
    {
        $this->validateClassnameImplementsInterface($this->gamePlayClassname, GamePlay::class);
        return $this->gamePlayClassname;
    }

    /**
     * @inheritDoc
     */
    public function getGameMoveFactoryClassname(): string
    {
        $this->validateClassnameImplementsInterface($this->gameMoveFactoryClassname, GameMoveFactory::class);
        return $this->gameMoveFactoryClassname;
    }

    /**
     * @throws GameBoxException
     */
    protected function validateClassnameImplementsInterface(string $class, string $interface): void
    {
        if (!class_exists($class) || !in_array($interface, class_implements($class))) {
            throw new GameBoxException(GameBoxException::MESSAGE_INCORRECT_CONFIGURATION);
        }
    }
}
