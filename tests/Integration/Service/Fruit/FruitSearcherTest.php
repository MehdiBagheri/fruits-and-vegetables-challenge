<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service\Fruit;

use App\Entity\Fruit;
use App\Service\Fruit\FruitSearcher;
use App\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Test;

class FruitSearcherTest extends IntegrationTestCase
{
    #[Test]
    public function shouldReturnEmptyArrayWhenNoFruits(): void
    {
        $result = $this->sut()->search();

        $this->assertTrue([] === $result);
    }

    #[Test]
    public function shouldReturnAllFruitsWhenEmptySearchTerm(): void
    {
        $this->createTestFruit('Apple', 1000);
        $this->createTestFruit('Banana', 800);
        $this->createTestFruit('Orange', 1500);

        $result = $this->sut()->search();

        $this->assertCount(3, $result);
        $this->assertContainsOnlyInstancesOf(Fruit::class, $result);
    }

    #[Test]
    public function shouldFilterFruitsByName(): void
    {
        $this->createTestFruit('Apple', 1000);
        $this->createTestFruit('Banana', 800);
        $this->createTestFruit('Pineapple', 1500);

        $result = $this->sut()->search('apple');

        $this->assertCount(2, $result);

        $fruitNames = array_map(fn (Fruit $f) => $f->getName(), $result);
        $this->assertContains('Apple', $fruitNames);
        $this->assertContains('Pineapple', $fruitNames);
        $this->assertNotContains('Banana', $fruitNames);
    }

    #[Test]
    public function shouldReturnEmptyArrayWhenNoMatches(): void
    {
        $this->createTestFruit('Apple', 1000);
        $this->createTestFruit('Banana', 800);

        $result = $this->sut()->search('orange');

        $this->assertTrue([] === $result);
    }

    #[Test]
    public function shouldPerformCaseInsensitiveSearch(): void
    {
        $this->createTestFruit('Apple', 1000);
        $this->createTestFruit('BANANA', 800);

        $result = $this->sut()->search('APPLE');

        $this->assertCount(1, $result);
        $this->assertEquals('Apple', $result[0]->getName());
    }

    private function createTestFruit(string $name, int $quantityInGrams): void
    {
        $fruit = new Fruit();
        $fruit->setName($name);
        $fruit->setQuantity($quantityInGrams);

        $this->entityManager->persist($fruit);
        $this->entityManager->flush();
    }

    private function sut(): FruitSearcher
    {
        return self::getContainer()->get(FruitSearcher::class);
    }
}
