<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller\Response\Serializer;

use App\Controller\Response\Serializer\FruitApiResponseNormalizer;
use App\Entity\Fruit;
use App\Entity\Vegetable;
use App\Enum\Unit;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FruitApiResponseNormalizerTest extends TestCase
{
    #[Test]
    public function shouldNormalizeFruitWithDefaultGramUnit(): void
    {
        $fruit = $this->createFakeFruit('Apple', 1500);
        $normalizer = new FruitApiResponseNormalizer();
        $result = $normalizer->normalize($fruit);

        $this->assertEquals([
            'id' => 1,
            'name' => 'Apple',
            'quantity' => 1500.0,
            'unit' => 'g',
            'links' => ['self' => '/fruits/1'],
        ], $result);
    }

    #[Test]
    public function shouldNormalizeFruitWithKgUnit(): void
    {
        $fruit = $this->createFakeFruit('Banana', 2000);
        $normalizer = new FruitApiResponseNormalizer();
        $result = $normalizer->normalize($fruit, null, ['unit' => Unit::KG]);

        $this->assertEquals([
            'id' => 1,
            'name' => 'Banana',
            'quantity' => 2.0,
            'unit' => 'kg',
            'links' => ['self' => '/fruits/1'],
        ], $result);
    }

    #[Test]
    public function shouldNormalizeFruitWithExplicitGramUnit(): void
    {
        $fruit = $this->createFakeFruit('Orange', 800);
        $normalizer = new FruitApiResponseNormalizer();
        $result = $normalizer->normalize($fruit, 'json', ['unit' => Unit::GRAM]);

        $this->assertEquals(800.0, $result['quantity']);
        $this->assertEquals('g', $result['unit']);
        $this->assertEquals('/fruits/1', $result['links']['self']);
    }

    #[Test]
    public function shouldSupportNormalizationForFruit(): void
    {
        $fruit = new Fruit();
        $normalizer = new FruitApiResponseNormalizer();

        $this->assertTrue($normalizer->supportsNormalization($fruit));
    }

    #[Test]
    public function shouldNotSupportNormalizationForOtherObjects(): void
    {
        $vegetable = new Vegetable();
        $normalizer = new FruitApiResponseNormalizer();

        $this->assertFalse($normalizer->supportsNormalization($vegetable));
        $this->assertFalse($normalizer->supportsNormalization(new \stdClass()));
        $this->assertFalse($normalizer->supportsNormalization('string'));
        $this->assertFalse($normalizer->supportsNormalization(123));
        $this->assertFalse($normalizer->supportsNormalization([]));
        $this->assertFalse($normalizer->supportsNormalization(null));
    }

    #[Test]
    public function shouldReturnCorrectSupportedTypes(): void
    {
        $normalizer = new FruitApiResponseNormalizer();
        $supportedTypes = $normalizer->getSupportedTypes('json');

        $this->assertArrayHasKey(Fruit::class, $supportedTypes);
        $this->assertTrue($supportedTypes[Fruit::class]);
        $this->assertCount(1, $supportedTypes);
    }

    #[Test]
    public function shouldThrowExceptionWhenGeneratingLinksForNonFruit(): void
    {
        $vegetable = new Vegetable();
        $normalizer = new FruitApiResponseNormalizer();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported entity type');

        $reflection = new \ReflectionClass($normalizer);
        $generateLinksMethod = $reflection->getMethod('generateLinks');
        $generateLinksMethod->setAccessible(true);
        $generateLinksMethod->invoke($normalizer, $vegetable);
    }

    #[Test]
    public function shouldHandleSmallQuantities(): void
    {
        $fruit = $this->createFakeFruit('Berry', 1);
        $normalizer = new FruitApiResponseNormalizer();

        $gramResult = $normalizer->normalize($fruit, null, ['unit' => Unit::GRAM]);
        $this->assertEquals(1.0, $gramResult['quantity']);

        $kgResult = $normalizer->normalize($fruit, null, ['unit' => Unit::KG]);
        $this->assertEquals(0.001, $kgResult['quantity']);
    }

    private function createFakeFruit(string $name, int $quantity): Fruit
    {
        $fruit = new Fruit();
        $reflection = new \ReflectionClass($fruit);
        $id = $reflection->getProperty('id');
        $id->setAccessible(true);
        $id->setValue($fruit, 1);
        $fruit->setName($name);
        $fruit->setQuantity($quantity);

        return $fruit;
    }
}
