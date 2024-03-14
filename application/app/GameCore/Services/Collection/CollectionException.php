<?php

namespace App\GameCore\Services\Collection;

class CollectionException extends \Exception
{
    public const MESSAGE_MISSING_KEY = 'Key missing in collection';
    public const MESSAGE_NO_ELEMENTS = 'Collection is empty';
}
