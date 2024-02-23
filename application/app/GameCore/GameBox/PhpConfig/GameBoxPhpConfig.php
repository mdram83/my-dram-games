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
        ) {
            throw new GameBoxException(GameBoxException::MESSAGE_INCORRECT_CONFIGURATION);
        }

        $this->description = $box['description'] ?? null;
        $this->durationInMinutes = $box['durationInMinutes'] ?? null;
        $this->minPlayerAge = $box['minPlayerAge'] ?? null;

        $this->isActive = $box['isActive'];
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

    public function getNumberOfPlayersDescription(): string
    {
        $numberOfPlayers = $this->getGameSetup()->getNumberOfPlayers();

        if (!$this->hasConsecutiveNumberOfPlayers($numberOfPlayers)) {
            return implode(', ', $numberOfPlayers);
        }

        if (count($numberOfPlayers) === 1) {
            return $numberOfPlayers[0];
        }

        return min($numberOfPlayers) . '-' . max($numberOfPlayers);
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
            'gameSetup' => $this->getGameSetup()->getAllOptions(),
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
