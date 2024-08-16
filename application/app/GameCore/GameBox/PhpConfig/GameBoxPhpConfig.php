<?php

namespace App\GameCore\GameBox\PhpConfig;

use App\GameCore\GameBox\GameBox;
use App\GameCore\GameBox\GameBoxException;
use App\GameCore\GameSetup\GameSetup;
use Illuminate\Support\Facades\Config;

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

    /**
     * @throws GameBoxException
     */
    public function __construct(string $slug, GameSetup $gameSetup)
    {
        $this->slug = $slug;
        $this->gameSetup = $gameSetup;

        if (!$box = Config::get('games.box.' . $this->slug)) {
            throw new GameBoxException(GameBoxException::MESSAGE_GAME_BOX_MISSING);
        }

        if (
            !($this->name = $box['name'] ?? '')
            || !($gameSetup->getNumberOfPlayers() ?? [])
            || !isset($box['isActive'])
            || !isset($box['isPremium'])
        ) {
            throw new GameBoxException(GameBoxException::MESSAGE_INCORRECT_CONFIGURATION);
        }

        $this->description = $box['description'] ?? null;
        $this->durationInMinutes = $box['durationInMinutes'] ?? null;
        $this->minPlayerAge = $box['minPlayerAge'] ?? null;

        $this->isActive = $box['isActive'];
        $this->isPremium = $box['isPremium'];
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
        $values = array_map(fn($value) => $value->value , $option->getAvailableValues());

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
                    $option->getAvailableValues()
                ),
                'defaultValue' => $option->getDefaultValue()->value,
                'type' => $option->getType(),
                'name' => $option->getName(),
                'description' => $option->getDescription(),
            ], $this->getGameSetup()->getAllOptions()),

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
}
