<?php

namespace App\GameCore\GameOptionValue;

use App\GameCore\GameOption\GameOptionClassRepository;

class GameOptionValueConverterEnum implements GameOptionValueConverter
{
    public function __construct(protected readonly GameOptionClassRepository $repository)
    {

    }

    /**
     * @throws GameOptionValueException
     */
    public function convert(mixed $value, string $gameOptionKey): GameOptionValue
    {
        try {
            return $this->repository->getOne($gameOptionKey)::getOptionValueClass()::from($value);
        } catch (\ValueError) {
            throw new GameOptionValueException(GameOptionValueException::MESSAGE_MISSING_VALUE);
        }
    }
}
