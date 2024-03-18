<?php

namespace App\GameCore\Services\Collection;

trait CollectionTraitUniqueKey
{
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
    }
}
