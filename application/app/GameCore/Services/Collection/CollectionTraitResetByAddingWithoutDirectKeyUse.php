<?php

namespace App\GameCore\Services\Collection;

trait CollectionTraitResetByAddingWithoutDirectKeyUse
{
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
}
