<?php

namespace App\Extensions\Utils\Php\Collection;

use Illuminate\Support\Collection as IlluminateCollection;
use MyDramGames\Utils\Php\Collection\CollectionEngine;
use MyDramGames\Utils\Php\Collection\CollectionEngineTrait;

/**
 * To support specific collection types, define TYPE_CLASS or TYPE_PRIMITIVE values in child class.
 * To support specific key mode, define KEY_MODE value (loose, forced, method) in child class.
 * See details in CollectionTrait PHPDoc
 */
class CollectionEngineLaravel implements CollectionEngine
{
    use CollectionEngineTrait;

    protected IlluminateCollection $items;

    final public function __construct(array $items = [])
    {
        $this->items = new IlluminateCollection($items);
    }

    /**
     * @inheritDoc
     */
    final public function count(): int
    {
        return $this->items->count();
    }

    /**
     * @inheritDoc
     */
    final public function isEmpty(): bool
    {
        return $this->items->isEmpty();
    }

    /**
     * @inheritDoc
     */
    final public function exist(mixed $key): bool
    {
        return $this->items->has($key);
    }

    /**
     * @inheritDoc
     */
    final public function keys(): array
    {
        return $this->items->keys()->toArray();
    }

    /**
     * @inheritDoc
     */
    final public function toArray(): array
    {
        return $this->items->all();
    }

    /**
     * @inheritDoc
     */
    final public function each(callable $callback): static
    {
        $this->items = $this->items->map($callback);
        return $this;
    }

    /**
     * @inheritDoc
     */
    final public function filter(callable $callback): static
    {
        $items = $this->items->filter($callback);
        return new static($items->all());
    }

    /**
     * @inheritDoc
     */
    final public function shuffle(): static
    {
        $keys = $this->items->keys()->all();
        shuffle($keys);

        $items = [];
        foreach ($keys as $key) {
            $items[$key] = $this->items->get($key);
        }

        return $this->reset($items);
    }

    /**
     * @inheritDoc
     */
    final public function sortKeys(callable $callback): static
    {
        $this->items = $this->items->sortKeysUsing($callback);
        return $this;
    }

    /**
     * @inheritDoc
     */
    final public function random(): mixed
    {
        $this->validateNotEmpty();
        return $this->items->random();
    }

    /**
     * @inheritDoc
     */
    final public function reset(array $items = []): static
    {
        $this->items = new IlluminateCollection($items);
        return $this;
    }

    /**
     * @inheritDoc
     */
    final public function getOne(mixed $key): mixed
    {
        $this->validateExists($key);
        return $this->items->get($key);
    }

    /**
     * @inheritDoc
     */
    final public function getMany(array $keys): static
    {
        $this->validateKeysInputArray($keys);
        $this->validateExistMany($keys);

        $items = $this->items->filter(fn($item, $key) => in_array($key, $keys));
        return new static($items->all());
    }

    /**
     * @inheritDoc
     */
    final public function removeOne(mixed $key): void
    {
        $this->validateExists($key);
        $this->items->forget($key);
    }

    /**
     * @inheritDoc
     */
    final public function removeAll(): void
    {
        $this->items = new IlluminateCollection();
    }

    /**
     * @inheritDoc
     */
    public function pull(mixed $key): mixed
    {
        $this->validateExists($key);
        return $this->items->pull($key);
    }

    /**
     * @inheritDoc
     */
    final public function pullFirst(): mixed
    {
        $this->validateNotEmpty();
        return $this->items->shift();
    }

    /**
     * @inheritDoc
     */
    final public function pullLast(): mixed
    {
        $this->validateNotEmpty();
        return $this->items->pop();
    }

    /**
     * @inheritDoc
     */
    final public function clone(): static
    {
        return new self($this->items->all());
    }

    protected function insert(mixed $item, mixed $key = null): void
    {
        isset($key) ? $this->items->put($key, $item) : $this->items->push($item);
    }
}
