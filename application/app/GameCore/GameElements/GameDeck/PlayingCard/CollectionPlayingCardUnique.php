<?php

namespace App\GameCore\GameElements\GameDeck\PlayingCard;

use App\GameCore\Services\Collection\CollectionBase;
use App\GameCore\Services\Collection\CollectionException;
use App\GameCore\Services\Collection\CollectionTraitResetByAddingWithoutDirectKeyUse;
use App\GameCore\Services\Collection\CollectionTraitUniqueKey;

class CollectionPlayingCardUnique extends CollectionPlayingCard
{
    use CollectionTraitResetByAddingWithoutDirectKeyUse;
    use CollectionTraitUniqueKey;

    public const TYPE_CLASS = PlayingCard::class;

    /**
     * @throws CollectionException
     */
    public function add(mixed $element, mixed $key = null): static
    {
        $this->validateType($element);
        $this->validateUnique($element->getKey(), $element);
        return parent::add($element, $element->getKey());
    }
}
