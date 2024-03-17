<?php

namespace App\GameCore\Services\Collection\Laravel;

use App\GameCore\Services\Collection\Collection;
use App\GameCore\Services\Collection\CollectionException;
use Illuminate\Support\Collection as IlluminateCollection;

class CollectionLaravel implements Collection
{
    protected IlluminateCollection $collection;

    public function __construct(array $elements = [])
    {
        $this->collection = new IlluminateCollection($elements);
    }

    public function count(): int
    {
        return $this->collection->count();
    }

    public function isEmpty(): bool
    {
        return $this->collection->isEmpty();
    }

    public function exist(mixed $key): bool
    {
        return $this->collection->has($key);
    }

    public function toArray(): array
    {
        return $this->collection->all();
    }

    public function each(callable $callback): static
    {
        $this->collection = $this->collection->map($callback);
        return $this;
    }

    public function shuffle(): static
    {
        $this->collection = $this->collection->shuffle();
        return $this;
    }

    public function random(): mixed
    {
        return $this->collection->random();
    }

    public function assignKeys(callable $callback): static
    {
        $this->collection = $this->collection->keyBy($callback);
        return $this;
    }

    public function reset(array $elements = []): static
    {
        $this->collection = new IlluminateCollection($elements);
        return $this;
    }

    /**
     * @throws CollectionException
     */
    public function add(mixed $element, mixed $key = null): static
    {
        if ($this->exist($key)) {
            throw new CollectionException(CollectionException::MESSAGE_DUPLICATE);
        }

        if (isset($key)) {
            $this->collection->put($key, $element);
        } else {
            $this->collection->push($element);
        }

        return $this;
    }

    /**
     * @throws CollectionException
     */
    public function getOne(mixed $key): mixed
    {
        if (!$this->exist($key)) {
            throw new CollectionException(CollectionException::MESSAGE_MISSING_KEY);
        }

        return $this->collection->get($key);
    }

    /**
     * @throws CollectionException
     */
    public function removeOne(mixed $key): void
    {
        if (!$this->exist($key)) {
            throw new CollectionException(CollectionException::MESSAGE_MISSING_KEY);
        }

        $this->collection->forget($key);
    }

    public function removeAll(): void
    {
        $this->collection = new IlluminateCollection([]);
    }

    /**
     * @throws CollectionException
     */
    public function pullFirst(): mixed
    {
        if ($this->isEmpty()) {
            throw new CollectionException(CollectionException::MESSAGE_NO_ELEMENTS);
        }

        return $this->collection->shift();
    }

    public function pullLast(): mixed
    {
        if ($this->isEmpty()) {
            throw new CollectionException(CollectionException::MESSAGE_NO_ELEMENTS);
        }

        return $this->collection->pop();
    }
}
