<?php

namespace App\GameCore\Services\Collection;

use App\GameCore\GameOptionValue\GameOptionValue;

class CollectionGameOptionValueInput extends CollectionBase implements Collection
{
    use CollectionTraitUniqueKey;

    public const TYPE_CLASS = GameOptionValue::class;

    /**
     * @throws CollectionException
     */
    public function add(mixed $element, mixed $key = null): static
    {
        $this->validateType($element);
        $this->validateUnique($key, $element);

        $this->collectionHandler = $this->collectionHandler->add($element, $key);
        return $this;
    }

    /**
     * @throws CollectionException
     */
    public function reset(array $elements = []): static
    {
        $this->collectionHandler->removeAll();

        foreach($elements as $key => $element) {
            $this->add($element, $key);
        }

        return $this;
    }
}
