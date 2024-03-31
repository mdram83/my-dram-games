<?php

namespace App\GameCore\GameRecord;

use App\GameCore\Services\Collection\CollectionBase;
use App\GameCore\Services\Collection\CollectionException;

class CollectionGameRecord extends CollectionBase
{
    public const TYPE_CLASS = GameRecord::class;

    /**
     * @throws CollectionException
     */
    public function add(mixed $element, mixed $key = null): static
    {
        $this->validateType($element);
        return parent::add($element, $key);
    }

    /**
     * @throws CollectionException
     */
    public function reset(array $elements = []): static
    {
        foreach ($elements as $element) {
            $this->validateType($element);
        }

        return parent::reset($elements);
    }
}
