<?php

namespace App\Games\Thousand\Tools;

use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\CollectionBase;
use App\GameCore\Services\Collection\CollectionException;
use App\GameCore\Services\Collection\CollectionTraitResetByAddingWithoutDirectKeyUse;
use App\GameCore\Services\Collection\CollectionTraitUniqueKey;

class CollectionPlayerDataThousand extends CollectionBase
{
    use CollectionTraitResetByAddingWithoutDirectKeyUse;
    use CollectionTraitUniqueKey;

    public const TYPE_CLASS = PlayerDataThousand::class;

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

        $key = $element->getId();
        $this->validateUnique($key, $element);

        $this->collectionHandler = $this->collectionHandler->add($element, $key);
        return $this;
    }

    public function getFor(Player $player): PlayerDataThousand
    {
        return $this->getOne($player->getId());
    }
}
