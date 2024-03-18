<?php

namespace App\GameCore\Services\Collection;

use App\GameCore\Player\Player;

class CollectionGamePlayPlayers extends CollectionBase implements Collection
{
    use CollectionTraitResetByAddingWithoutDirectKeyUse;
    use CollectionTraitUniqueKey;

    public const TYPE_CLASS = Player::class;

    protected function keysCallable(): callable
    {
        return fn($item, $key) => $item->getId();
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
