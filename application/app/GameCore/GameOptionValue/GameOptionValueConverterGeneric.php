<?php

namespace App\GameCore\GameOptionValue;

use App\GameCore\GameOption\GameOptionClassRepository;
use MyDramGames\Core\Exceptions\GameOptionValueException;
use MyDramGames\Core\GameOption\GameOptionValue;

readonly class GameOptionValueConverterGeneric implements GameOptionValueConverter
{
    public function __construct(protected GameOptionClassRepository $repository)
    {

    }

    /**
     * @throws GameOptionValueException
     */
    public function convert(mixed $value, string $gameOptionKey): GameOptionValue
    {
        try {
            return ($this->repository->getValueClassname($gameOptionKey))::fromValue($value);
        } catch (\Exception) {
            throw new GameOptionValueException(GameOptionValueException::MESSAGE_MISSING_VALUE);
        }
    }
}
