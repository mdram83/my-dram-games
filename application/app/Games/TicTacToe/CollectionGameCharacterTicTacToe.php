<?php

namespace App\Games\TicTacToe;

use App\GameCore\Services\Collection\Collection;
use App\GameCore\Services\Collection\CollectionBase;
use App\GameCore\Services\Collection\CollectionException;
use App\GameCore\Services\Collection\CollectionTraitResetByAddingWithoutDirectKeyUse;

class CollectionGameCharacterTicTacToe extends CollectionBase implements Collection
{
    use CollectionTraitResetByAddingWithoutDirectKeyUse;

    public const TYPE_CLASS = GameCharacterTicTacToe::class;

    protected function keysCallable(): callable
    {
        return fn($item, $key) => $item->getName();
    }

    /**
     * @throws CollectionException
     */
    protected function validateUnique(mixed $key, mixed $element): void
    {
        if (!isset($key)) {
            throw new CollectionException(CollectionException::MESSAGE_MISSING_KEY);
        }

        if ($this->exist($key)) {
            throw new CollectionException(CollectionException::MESSAGE_DUPLICATE);
        }

        if (
            !$this
                ->filter(fn($item, $key) => $item->getPlayer()->getId() === $element->getPlayer()->getId())
                ->isEmpty()
        ) {
            throw new CollectionException(CollectionException::MESSAGE_DUPLICATE);
        }
    }

    /**
     * @throws CollectionException
     */
    public function add(mixed $element, mixed $key = null): static
    {
        $this->validateType($element);

        $key = $element->getName();
        $this->validateUnique($key, $element);

        $this->collectionHandler = $this->collectionHandler->add($element, $key);
        return $this;
    }
}
