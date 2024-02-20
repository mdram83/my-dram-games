<?php

namespace App\GameCore\GameDefinition\PhPConfig;

use App\GameCore\GameDefinition\GameDefinition;
use App\GameCore\GameDefinition\GameDefinitionException;
use Illuminate\Support\Facades\Config;

class GameDefinitionPhpConfig implements GameDefinition
{

    private string $slug;
    private string $name;
    private ?string $description;
    private array $numberOfPlayers;
    private ?int $durationInMinutes;
    private ?int $minPlayerAge;
    private bool $isActive;

    public function __construct(string $slug)
    {
        $this->slug = $slug;

        if (!$definition = Config::get('games.gameDefinition.' . $this->slug)) {
            throw new GameDefinitionException(GameDefinitionException::MESSAGE_GAME_DEFINITION_MISSING);
        }

        if (
            !($this->name = $definition['name'] ?? '')
            || !($this->numberOfPlayers = $definition['numberOfPlayers'] ?? [])
            || !isset($definition['isActive'])
        ) {
            throw new GameDefinitionException(GameDefinitionException::MESSAGE_INCORRECT_CONFIGURATION);
        }

        $this->description = $definition['description'] ?? null;
        $this->durationInMinutes = $definition['durationInMinutes'] ?? null;
        $this->minPlayerAge = $definition['minPlayerAge'] ?? null;

        $this->isActive = $definition['isActive'];
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

    public function getNumberOfPlayers(): array
    {
        return $this->numberOfPlayers;
    }

    public function getNumberOfPlayersDescription(): string
    {
        if (!$this->hasConsecutiveNumberOfPlayers()) {
            return implode(', ', $this->numberOfPlayers);
        }

        if (count($this->numberOfPlayers) === 1) {
            return $this->numberOfPlayers[0];
        }

        return min($this->numberOfPlayers) . '-' . max($this->numberOfPlayers);
    }

    public function toArray(): array
    {
        return [
            'slug' => $this->getSlug(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'numberOfPlayers' => $this->getNumberOfPlayers(),
            'numberOfPlayersDescription' => $this->getNumberOfPlayersDescription(),
            'durationInMinutes' => $this->getDurationInMinutes(),
            'minPlayerAge' => $this->getMinPlayerAge(),
            'isActive' => $this->isActive(),
        ];
    }

    private function hasConsecutiveNumberOfPlayers(): bool
    {
        for ($i = 1; $i < count($this->numberOfPlayers); $i++) {
            if ($this->numberOfPlayers[$i] - $this->numberOfPlayers[$i - 1] !== 1) {
                return false;
            }
        }
        return true;
    }
}
