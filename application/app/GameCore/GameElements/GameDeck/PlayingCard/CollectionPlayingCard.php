<?php

namespace App\GameCore\GameElements\GameDeck\PlayingCard;

use App\GameCore\Services\Collection\CollectionBase;
use App\GameCore\Services\Collection\CollectionException;
use App\GameCore\Services\Collection\CollectionTraitResetByAddingWithoutDirectKeyUse;

class CollectionPlayingCard extends CollectionBase
{
    use CollectionTraitResetByAddingWithoutDirectKeyUse;

    public const TYPE_CLASS = PlayingCard::class;

    /**
     * @throws CollectionException
     */
    public function add(mixed $element, mixed $key = null): static
    {
        $this->validateType($element);
        return parent::add($element, $key);
    }
}
