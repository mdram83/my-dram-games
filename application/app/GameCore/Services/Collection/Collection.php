<?php

namespace App\GameCore\Services\Collection;

interface Collection
{
    public function count(): int;
    public function isEmpty(): bool;
    public function exist(mixed $key): bool;
    public function toArray(): array;

    public function each(callable $callback): static;
    public function shuffle(): static;
    public function random(): mixed;
    public function assignKeys(callable $callback): static;

    public function reset(array $elements = []): static;
    public function add(mixed $element, mixed $key = null): static;
    public function getOne(mixed $key): mixed;
    public function removeOne(mixed $key): void;
    public function removeAll(): void;
    public function pullFirst(): mixed;
    public function pullLast(): mixed;
}
