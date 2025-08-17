<?php

declare(strict_types=1);

namespace App\Utility;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @implements \IteratorAggregate<TKey, TValue>
 */
final readonly class ArrayCollection implements \IteratorAggregate, \Countable
{
    /**
     * @param array<TKey, TValue> $elements
     */
    public function __construct(private array $elements = [])
    {
    }

    /**
     * @return \Traversable<TKey, TValue>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->elements);
    }

    public function get(string|int $key): mixed
    {
        return $this->elements[$key] ?? null;
    }

    /**
     * @return array<TKey, TValue>
     */
    public function list(): array
    {
        return $this->elements;
    }

    /**
     * @return static<TKey, TValue>
     */
    public function remove(string|int $key): self
    {
        $elements = $this->elements;
        unset($elements[$key]);

        return $this->createFrom($elements);
    }

    /**
     * @return self<TKey, TValue>
     */
    public function add(mixed $element): self
    {
        $elements = $this->elements;
        $elements[] = $element;

        return $this->createFrom($elements);
    }

    /**
     * @return self<TKey, TValue>
     */
    public function reduce(callable $callback, mixed $initial = null): self
    {
        return new self(array_reduce($this->elements, $callback, $initial));
    }

    public function count(): int
    {
        return count($this->elements);
    }

    /**
     * @param array<TKey, TValue> $elements
     *
     * @return self<TKey, TValue>
     */
    private function createFrom(array $elements): self
    {
        return new self($elements);
    }
}
