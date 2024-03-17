<?php

namespace App\GameCore\Services\Collection;

use App\GameCore\GameOption\GameOption;

class CollectionGameOption extends CollectionBase implements Collection
{
    public const TYPE_CLASS = GameOption::class;

    protected function keysCallable(): callable
    {
        return fn($item, $key) => $item->getKey();
    }

    /**
     * @throws CollectionException
     */
    protected function validateUnique(mixed $key, mixed $element): void
    {
        if ($this->exist($key)) {
            throw new CollectionException(CollectionException::MESSAGE_DUPLICATE);
        }
    }

    /**
     * @throws CollectionException
     */
    public function reset(array $elements = []): static
    {
        $this->collectionHandler->removeAll();

        foreach($elements as $element) {
            $this->add($element);
        }

        return $this;
    }

    /**
     * @throws CollectionException
     */
    public function add(mixed $element, mixed $key = null): static
    {
        $this->validateType($element);

        $key = $element->getKey();
        $this->validateUnique($key, $element);

        $this->collectionHandler = $this->collectionHandler->add($element, $key);
        return $this;
    }

}
