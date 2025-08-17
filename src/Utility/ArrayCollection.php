<?php

declare(strict_types=1);

namespace App\Utility;

final readonly class ArrayCollection implements \IteratorAggregate, \Countable
{
    public function __construct(private array $elements = [])
    {
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->elements);
    }

    public function get(string|int $key): mixed
    {
        return $this->elements[$key] ?? null;
    }

    public function list(): array
    {
        return $this->elements;
    }

    public function remove(string|int $key): self
    {
        $elements = $this->elements;
        unset($elements[$key]);

        return $this->createFrom($elements);
    }

    public function add(mixed $element): self
    {
        $elements = $this->elements;
        $elements[] = $element;

        return $this->createFrom($elements);
    }

    public function count(): int
    {
        return count($this->elements);
    }

    private function createFrom(array $elements): self
    {
        return new self($elements);
    }
}
