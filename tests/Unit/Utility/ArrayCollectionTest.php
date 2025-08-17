<?php

declare(strict_types=1);

namespace App\Tests\Unit\Utility;

use App\Utility\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ArrayCollection::class)]
final class ArrayCollectionTest extends TestCase
{
    #[Test]
    public function shouldBeTraversable(): void
    {
        $collection = new ArrayCollection();
        $this->assertInstanceOf(\Traversable::class, $collection->getIterator());
    }

    #[Test]
    public function shouldBeCountable(): void
    {
        $collection = new ArrayCollection();
        $this->assertInstanceOf(\Countable::class, $collection);
    }

    #[Test]
    public function shouldBeEmptyArrayOnInitialization(): void
    {
        $collection = new ArrayCollection();
        $this->assertTrue(0 === $collection->count());
    }

    #[Test]
    public function shouldAddElementImmutable(): void
    {
        $collection = new ArrayCollection();
        $collectionUpdated = $collection->add(['key' => 'value']);
        $this->assertTrue(0 === $collection->count());
        $this->assertTrue(1 === $collectionUpdated->count());
    }

    #[Test]
    public function shouldRemoveElementImmutable(): void
    {
        $collection = new ArrayCollection(['key' => 'value']);
        $collectionUpdated = $collection->remove('key');
        $this->assertTrue(1 === $collection->count());
        $this->assertTrue(0 === $collectionUpdated->count());
    }

    #[Test]
    public function shouldApplyReduceOnCollection(): void
    {
        $collection = new ArrayCollection([[1, 2], [3, 4]]);

        $result = $collection->reduce(function ($carry, $item) {
            return array_merge($carry, $item);
        }, []);
        $this->assertEquals(new ArrayCollection([1, 2, 3, 4]), $result);
    }

    #[Test]
    public function shouldReturnArray(): void
    {
        $array = [1, 2, 3, 4];
        $collection = new ArrayCollection($array);

        $this->assertSame($array, $collection->list());
    }

    #[Test]
    public function shouldSearchWithCallback(): void
    {
        $collection = new ArrayCollection([1, 2, 3, 4, 5]);

        $result = $collection->search(fn ($item) => $item > 3);

        $this->assertEquals([4, 5], array_values($result->list()));
        $this->assertEquals(2, $result->count());
    }

    #[Test]
    public function shouldHandleSearchOnEmptyCollection(): void
    {
        $collection = new ArrayCollection([]);

        $result = $collection->search(fn ($item) => true);

        $this->assertEquals(new ArrayCollection([]), $result);
        $this->assertTrue(0 === $result->count());
    }

    #[Test]
    public function shouldSearchImmutable(): void
    {
        $collection = new ArrayCollection([1, 2, 3, 4, 5]);

        $result = $collection->search(fn ($item) => $item > 3);

        $this->assertTrue(5 === $collection->count()); // Original unchanged
        $this->assertTrue(2 === $result->count());     // New collection
        $this->assertNotSame($collection, $result);
    }

    #[Test]
    public function shouldSearchObjectsByProperty(): void
    {
        $fruits = [
            (object) ['name' => 'apple', 'quantity' => 100],
            (object) ['name' => 'banana', 'quantity' => 200],
            (object) ['name' => 'orange', 'quantity' => 150],
        ];
        $collection = new ArrayCollection($fruits);

        $result = $collection->search(fn ($fruit) => $fruit->quantity > 120);

        $this->assertTrue(2 === $result->count());
    }
}
