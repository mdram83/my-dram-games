<?php

namespace App\GameCore\Services\Collection;

class CollectionBase implements Collection
{
    public const TYPE_CLASS = null;
    public const TYPE_PRIMITIVE = null;

    protected Collection $collectionHandler;

    final public function __construct(Collection $collectionHandler, array $elements = [])
    {
        $this->collectionHandler = $collectionHandler;
        $this->reset($elements);
    }

    /**
     * Adjust this function in specific implementation to validate uniqueness of elements (key, value, else) if required
     * Basic use for collections without specific keys can be 'return fn($item, $key) => $key';
     */
    protected function keysCallable(): callable
    {
        return fn($item, $key) => $key;
    }

    /**
     * Adjust this function in specific implementation to validate uniqueness of elements (key, value, else) if required
     */
    protected function validateUnique(mixed $key, mixed $element): void
    {

    }

    /**
     * Adjust this function in specific implementation to utilize validateUnique if required
     */
    public function reset(array $elements = []): static
    {
        $this->collectionHandler = $this->collectionHandler->reset($elements);
        return $this;
    }

    /**
     * Adjust this function in specific implementation to utilize validateType and validateUnique if required
     */
    public function add(mixed $element, mixed $key = null): static
    {
        $this->collectionHandler = $this->collectionHandler->add($element, $key);
        return $this;
    }

    /**
     * @throws CollectionException
     */
    final protected function validateType(mixed $element): void
    {
        $typeClass = $this::TYPE_CLASS;
        if (isset($typeClass) && !($element instanceof $typeClass)) {
            throw new CollectionException(CollectionException::MESSAGE_INCOMPATIBLE);
        }

        $typePrimitive = $this::TYPE_PRIMITIVE;
        if (isset($typePrimitive) && !(gettype($element) === $typePrimitive)) {
            throw new CollectionException(CollectionException::MESSAGE_INCOMPATIBLE);
        }
    }

    final public function count(): int
    {
        return $this->collectionHandler->count();
    }

    final public function isEmpty(): bool
    {
        return $this->collectionHandler->isEmpty();
    }

    final public function exist(mixed $key): bool
    {
        return $this->collectionHandler->exist($key);
    }

    final public function toArray(): array
    {
        return $this->collectionHandler->toArray();
    }

    final public function each(callable $callback): static
    {
        $this->collectionHandler = $this->collectionHandler->each($callback);
        return $this;
    }

    final public function filter(callable $callback): static
    {
        $elements = $this->collectionHandler->filter($callback)->toArray();
        $handler = clone $this->collectionHandler;
        $handler->reset();

        return new static($handler, $elements);
    }

    final public function shuffle(): static
    {
        $this->collectionHandler = $this->collectionHandler->shuffle();
        return $this->assignKeys($this->keysCallable());
    }

    final public function random(): mixed
    {
        return $this->collectionHandler->random();
    }

    final public function assignKeys(callable $callback): static
    {
        $this->collectionHandler = $this->collectionHandler->assignKeys($callback);
        return $this;
    }

    final public function getOne(mixed $key): mixed
    {
        return $this->collectionHandler->getOne($key);
    }

    final public function removeOne(mixed $key): void
    {
        $this->collectionHandler->removeOne($key);
    }

    final public function removeAll(): void
    {
        $this->collectionHandler->removeAll();
    }

    final public function pullFirst(): mixed
    {
        return $this->collectionHandler->pullFirst();
    }

    final public function pullLast(): mixed
    {
        return $this->collectionHandler->pullLast();
    }
}
