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
}
