<?php

namespace App\Games\TicTacToe\Elements;

use MyDramGames\Utils\Php\Collection\CollectionGeneric;

class GameCharacterTicTacToeCollectionGeneric extends CollectionGeneric implements GameCharacterTicTacToeCollection
{
    protected const ?string TYPE_CLASS = GameCharacterTicTacToe::class;
    protected const int KEY_MODE = self::KEYS_METHOD;

    protected function getItemKey(mixed $item): mixed
    {
        return $item->getName();
    }
}
