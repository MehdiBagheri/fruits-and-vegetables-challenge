<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller\Response\Serializer;

use App\Controller\Response\Serializer\VegetableApiResponseNormalizer;
use App\Entity\Fruit;
use App\Entity\Vegetable;
use App\Enum\Unit;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class VegetableApiResponseNormalizerTest extends TestCase
{
    #[Test]
    public function shouldNormalizeVegetableWithDefaultGramUnit(): void
    {
        $vegetable = $this->createFakeVegetable('Carrot', 1500);
        $normalizer = new VegetableApiResponseNormalizer();
        $result = $normalizer->normalize($vegetable);

        $this->assertEquals([
            'id' => 1,
            'name' => 'Carrot',
            'quantity' => 1500.0,
            'unit' => 'g',
            'links' => ['self' => '/vegetables/1'],
        ], $result);
    }

    #[Test]
    public function shouldNormalizeVegetableWithKgUnit(): void
    {
        $vegetable = $this->createFakeVegetable('Potato', 2000);
        $normalizer = new VegetableApiResponseNormalizer();
        $result = $normalizer->normalize($vegetable, null, ['unit' => Unit::KG]);

        $this->assertEquals([
            'id' => 1,
            'name' => 'Potato',
            'quantity' => 2.0,
            'unit' => 'kg',
            'links' => ['self' => '/vegetables/1'],
        ], $result);
    }

    #[Test]
    public function shouldNormalizeVegetableWithExplicitGramUnit(): void
    {
        $vegetable = $this->createFakeVegetable('Broccoli', 800);
        $normalizer = new VegetableApiResponseNormalizer();
        $result = $normalizer->normalize($vegetable, 'json', ['unit' => Unit::GRAM]);

        $this->assertEquals(800.0, $result['quantity']);
        $this->assertEquals('g', $result['unit']);
        $this->assertEquals('/vegetables/1', $result['links']['self']);
    }

    #[Test]
    public function shouldSupportNormalizationForVegetable(): void
    {
        $vegetable = new Vegetable();
        $normalizer = new VegetableApiResponseNormalizer();

        $this->assertTrue($normalizer->supportsNormalization($vegetable));
    }

    #[Test]
    public function shouldNotSupportNormalizationForOtherObjects(): void
    {
        $fruit = new Fruit();
        $normalizer = new VegetableApiResponseNormalizer();

        $this->assertFalse($normalizer->supportsNormalization($fruit));
        $this->assertFalse($normalizer->supportsNormalization(new \stdClass()));
        $this->assertFalse($normalizer->supportsNormalization('string'));
        $this->assertFalse($normalizer->supportsNormalization(123));
        $this->assertFalse($normalizer->supportsNormalization([]));
        $this->assertFalse($normalizer->supportsNormalization(null));
    }

    #[Test]
    public function shouldReturnCorrectSupportedTypes(): void
    {
        $normalizer = new VegetableApiResponseNormalizer();
        $supportedTypes = $normalizer->getSupportedTypes('json');

        $this->assertArrayHasKey(Vegetable::class, $supportedTypes);
        $this->assertTrue($supportedTypes[Vegetable::class]);
        $this->assertCount(1, $supportedTypes);
    }

    #[Test]
    public function shouldThrowExceptionWhenGeneratingLinksForNonVegetable(): void
    {
        $fruit = new Fruit();
        $normalizer = new VegetableApiResponseNormalizer();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported entity type');

        $reflection = new \ReflectionClass($normalizer);
        $generateLinksMethod = $reflection->getMethod('generateLinks');
        $generateLinksMethod->setAccessible(true);
        $generateLinksMethod->invoke($normalizer, $fruit);
    }

    #[Test]
    public function shouldHandleSmallQuantities(): void
    {
        $vegetable = $this->createFakeVegetable('Pea', 1);
        $normalizer = new VegetableApiResponseNormalizer();

        $gramResult = $normalizer->normalize($vegetable, null, ['unit' => Unit::GRAM]);
        $this->assertEquals(1.0, $gramResult['quantity']);

        $kgResult = $normalizer->normalize($vegetable, null, ['unit' => Unit::KG]);
        $this->assertEquals(0.001, $kgResult['quantity']);
    }

    private function createFakeVegetable(string $name, int $quantity): Vegetable
    {
        $vegetable = new Vegetable();
        $reflection = new \ReflectionClass($vegetable);
        $id = $reflection->getProperty('id');
        $id->setAccessible(true);
        $id->setValue($vegetable, 1);
        $vegetable->setName($name);
        $vegetable->setQuantity($quantity);

        return $vegetable;
    }
}