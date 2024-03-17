<?php

namespace App\GameCore\Services\Collection;

use App\GameCore\GameOption\GameOption;

class CollectionGameOption extends CollectionBase implements Collection
{
    use CollectionTraitResetByAddingWithoutDirectKeyUse;
    use CollectionTraitUniqueKey;

    public const TYPE_CLASS = GameOption::class;

    protected function keysCallable(): callable
    {
        return fn($item, $key) => $item->getKey();
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
